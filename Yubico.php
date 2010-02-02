<?php
  /**
   * Class for verifying Yubico One-Time-Passcodes
   *
   * LICENSE:
   *
   * Copyright (c) 2007, 2008, 2009, 2010  Yubico AB.  All rights reserved.
   *
   * Redistribution and use in source and binary forms, with or without
   * modification, are permitted provided that the following conditions
   * are met:
   *
   * o Redistributions of source code must retain the above copyright
   *   notice, this list of conditions and the following disclaimer.
   * o Redistributions in binary form must reproduce the above copyright
   *   notice, this list of conditions and the following disclaimer in the
   *   documentation and/or other materials provided with the distribution.
   * o The names of the authors may not be used to endorse or promote
   *   products derived from this software without specific prior written
   *   permission.
   *
   * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
   * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
   * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
   * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
   * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
   * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
   * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
   * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
   * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
   * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
   * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
   *
   * @category    Auth
   * @package     Auth_Yubico
   * @author      Simon Josefsson <simon@yubico.com>, Olov Danielson <olov@yubico.com>
   * @copyright   2007, 2008, 2009, 2010 Yubico AB
   * @license     http://opensource.org/licenses/bsd-license.php New BSD License
   * @version     1.6
   * @link        http://www.yubico.com/
   */

require_once 'PEAR.php';

/**
 * Class for verifying Yubico One-Time-Passcodes
 *
 * Simple example:
 * <code>
 * require_once 'Auth/Yubico.php';
 * $otp = "ccbbddeertkrctjkkcglfndnlihhnvekchkcctif";
 *
 * # Generate a new id+key from https://api.yubico.com/get-api-key/
 * $yubi = &new Auth_Yubico('42', 'FOOBAR=');
 * $auth = $yubi->verify($otp);
 * if (PEAR::isError($auth)) {
 *    print "<p>Authentication failed: " . $auth->getMessage();
 *    print "<p>Debug output from server: " . $yubi->getLastResponse();
 * } else {
 *    print "<p>You are authenticated!";
 * }
 * </code>
 */
class Auth_Yubico
{
	/**#@+
	 * @access private
	 */

	/**
	 * Yubico client ID
	 * @var string
	 */
	var $_id;

	/**
	 * Yubico client key
	 * @var string
	 */
	var $_key;

	/**
	 * URL part of validation server
	 * @var string
	 */
	var $_url;

	/**
	 * List with URL part of validation servers
	 * @var array
	 */
	var $_url_list;

	/**
	 * index to _url_list
	 * @var int
	 */
	var $_url_index;

	/**
	 * Query to server
	 * @var string
	 */
	var $_query;

	/**
	 * Response from server
	 * @var string
	 */
	var $_response;

	/**
	 * Flag whether to use https or not.
	 * @var string
	 */
	var $_https;

	/**
	 * Constructor
	 *
	 * Sets up the object
	 * @param    string  The client identity
	 * @param    string  The client MAC key (optional)
	 * @param    boolean Flag whether to use https (optional)
	 * @access public
	 */
	function Auth_Yubico($id, $key = '', $https = 0)
	{
		$this->_id =  $id;
		$this->_key = base64_decode($key);
		$this->_https = $https;
	}

	/**
	 * Specify to use a different URL part for verification.
	 * The default is "api.yubico.com/wsapi/verify".
	 *
	 * @param  string    New server URL part to use
	 * @access public
	 */
	function setURLpart($url)
	{
		$this->_url = $url;
	}

	/**
	 * Get URL part to use for validation.
	 *
	 * @return string		Server URL part
	 * @access public
	 */
	function getURLpart()
	{
		if ($this->_url) {
			return $this->_url;
		} else {
			return "api.yubico.com/wsapi/verify";
		}
	}


	/**
	 * Get next URL part from list to use for validation.
	 *
	 * @mixed string with URL part of false if no more URLs in list
	 * @access public
	 */
	function getNextURLpart()
	{
	  if ($this->_url_list) $url_list=$this->_url_list;
	  else $url_list=array('api1.yubico.com/wsapi/2.0/verify',
			       'api2.yubico.com/wsapi/2.0/verify', 
			       'api3.yubico.com/wsapi/2.0/verify', 
			       'api4.yubico.com/wsapi/2.0/verify',
			       'api5.yubico.com/wsapi/2.0/verify');
	  
	  if ($this->_url_index>=count($url_list)) return false;
	  else return $url_list[$this->_url_index++];
	}

	/**
	 * Resets index to URL list
	 *
	 * @access public
	 */
	function URLreset()
	{
	  $this->_url_index=0;
	}
	function addURLpart($URLpart) 
	{
	  $this->_url_list[]=$URLpart;
	}
	
	/**
	 * Return the last query sent to the server, if any.
	 *
	 * @return string		Output from server
	 * @access public
	 */
	function getLastQuery()
	{
		return $this->_query;
	}

	/**
	 * Return the last data received from the server, if any.
	 *
	 * @return string		Output from server
	 * @access public
	 */
	function getLastResponse()
	{
		return $this->_response;
	}

	/**
	 * Parse input string into password, yubikey prefix,
	 * ciphertext, and OTP.
	 *
	 * @param  string    Input string to parse
	 * @param  string    Optional delimiter re-class, default is '[:]'
	 * @return array     Keyed array with fields
	 * @access public
	 */
	function parsePasswordOTP($str, $delim = '[:]')
	{
		if (!preg_match("/^((.*)" . $delim . ")?" .
				"(([cbdefghijklnrtuvCBDEFGHIJKLNRTUV]{0,16})" .
				"([cbdefghijklnrtuvCBDEFGHIJKLNRTUV]{32}))$/",
				$str, $matches)) {
			return false;
		}
		$ret['password'] = $matches[2];
		$ret['otp'] = $matches[3];
		$ret['prefix'] = $matches[4];
		$ret['ciphertext'] = $matches[5];
		return $ret;
	}

	/* TODO? Add functions to get parsed parts of server response? */

	/**
	 * Parse parameters from last response
	 *
	 * example: getParameters("timestamp", "sessioncounter", "sessionuse");
	 *
	 * @param  array      Array with strings representing parameters to parse
	 * @return array      parameter array from last response
	 * @access public
	 */
	function getParameters($parameters)
	{
	  if ($parameters == null) {
	    $parameters = array('timestamp', 'sessioncounter', 'sessionuse');
	  }
	  $param_array = array();
	  foreach ($parameters as $param) {
	    if(!preg_match("/" . $param . "=([0-9]+)/", $this->_response, $out)) {
	      return PEAR::raiseError('Could not parse parameter ' . $param . ' from response');
	    }
	    $param_array[$param]=$out[1];
	  }
	  return $param_array;
	}

	/**
	 * Verify Yubico OTP
	 *
	 * @param string $token        Yubico OTP
	 * @param int $use_timestamp   1=>send request with &timestamp=1 to get timestamp
	 *                             and session information in the response
	 * @return mixed               PEAR error on error, true otherwise
	 * @access public
	 */
	function verify($token, $use_timestamp=null)
	{
		$ret = $this->parsePasswordOTP($token);
		if (!$ret) {
			return PEAR::raiseError('Could not parse Yubikey OTP');
		}

		$parameters = "id=" . $this->_id . "&otp=" . $ret['otp'];
		if ($use_timestamp) $parameters = $parameters . "&timestamp=1";
		/* Generate signature. */
		if($this->_key <> "") {
			$signature = base64_encode(hash_hmac('sha1', $parameters, $this->_key, true));
			$signature = preg_replace('/\+/', '%2B', $signature);
			$parameters .= '&h=' . $signature;
		}

		/* Support https. */
		if ($this->_https) {
		  $this->_query = "https://";
		} else {
		  $this->_query = "http://";
		}
		$this->_query .= $this->getURLpart();
		$this->_query .= "?";
		$this->_query .= $parameters;

		$ch = curl_init($this->_query);
		curl_setopt($ch, CURLOPT_USERAGENT, "PEAR Auth_Yubico");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$this->_response = curl_exec($ch);
		curl_close($ch);
		
		if(!preg_match("/status=([a-zA-Z0-9_]+)/", $this->_response, $out)) {
			return PEAR::raiseError('Could not parse response');
		}

		$status = $out[1];
		
		/* Verify signature. */
		if($this->_key <> "") {
			$rows = split("\r\n", $this->_response);
			while (list($key, $val) = each($rows)) {
				// = is also used in BASE64 encoding so we only replace the first = by # which is not used in BASE64
				$val = preg_replace('/=/', '#', $val, 1);
				$row = split("#", $val);
				$response[$row[0]] = $row[1];
			}

			$parameters=array("sessioncounter", "sessionuse", "status", "t", "timestamp"); 
			foreach ($parameters as $param) {
			  if ($response[$param]!=null) {
			    if ($check) $check = $check . '&';
			    $check = $check . $param . '=' . $response[$param];
			  }
			}
			  
			$checksignature = base64_encode(hash_hmac('sha1', $check, $this->_key, true));
			if($response[h] != $checksignature) {
			  return PEAR::raiseError('Checked Signature failed');
			}
		}
		
		if ($status != 'OK') {
			return PEAR::raiseError($status);
		}
		
		return true;
	}

	/**
	 * Verify Yubico OTP against multiple URLs
	 * Protocol specification 2.0 is used to construct validation requests
	 *
	 * @param string $token        Yubico OTP
	 * @param int $use_timestamp   1=>send request with &timestamp=1 to get timestamp
	 *                             and session information in the response
	 * @return mixed               PEAR error on error, true otherwise
	 * @access public
	 */
	function multi_verify($token, $use_timestamp=null, $wait_for_all=False,$sl=null, $timeout=null)
	{
	  $ret = $this->parsePasswordOTP($token);
	  if (!$ret) {
	    return PEAR::raiseError('Could not parse Yubikey OTP');
	  }
	  
	  $params = array('id'=>$this->_id, 
			  'otp'=>$ret['otp'],
			  'nonce'=>md5(uniqid(rand())));
	  /* Take care of protocol version 2 parameters */
	  if ($use_timestamp) $params['timestamp'] = 1;
	  if ($sl) $params['sl'] = $sl;
	  if ($timeout) $params['timeout'] = $timeout;
	  
	  /* Construct parameters string */
	  ksort($params);
	  foreach($params as $p=>$v) $parameters .= "&" . $p . "=" . $v;
	  $parameters = ltrim($parameters, "&");
	  
	  /* Generate signature. */
	  if($this->_key <> "") {
	    $signature = base64_encode(hash_hmac('sha1', $parameters, $this->_key, true));
	    $signature = preg_replace('/\+/', '%2B', $signature);
	    $parameters .= '&h=' . $signature;
	  }
	  
	  $this->_response=null;
	  $this->URLreset();
	  $mh = curl_multi_init();
	  $ch = array();
	  while($URLpart=$this->getNextURLpart()) 
	    {
	      /* Support https. */
	      if ($this->_https) {
		$this->_query = "https://";
	      } else {
		$this->_query = "http://";
	      }
	      $this->_query .= $URLpart;
	      $this->_query .= "?";
	      $this->_query .= $parameters;
	      
	      $handle = curl_init($this->_query);
	      curl_setopt($handle, CURLOPT_USERAGENT, "PEAR Auth_Yubico");
	      curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
	      curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
	      curl_setopt($handle, CURLOPT_FAILONERROR, true);
	      /* If timeout is set, we better apply it here as well
	       in case the validation server fails to follow it. 
	      */ 
	      if ($timeout) curl_setopt($handle, CURLOPT_TIMEOUT, $timeout);
	      curl_multi_add_handle($mh, $handle);
	      
	      $ch[$handle] = $handle;
	    }
	  
	  
	  $replay=False;
	  $valid=False;
	  do {
	    while (($mrc = curl_multi_exec($mh, $active)) == CURLM_CALL_MULTI_PERFORM)
	      ;
	    
	    while ($info = curl_multi_info_read($mh)) {
	      if ($info['result'] == CURL_OK) {
		$str = curl_multi_getcontent($info['handle']);

		if(preg_match("/status=([a-zA-Z0-9_]+)/", $str, $out)) {
		  
		  $status = $out[1];
		  $cinfo = curl_getinfo ($info['handle']);
		  
		  /* 
		   There are 3 cases.
		   1. OTP or Nonce values doesn't match and answer is considered neither valid or a replay
		   2. First signature is verified, then we consider the answer to be a replay or valid depending on the status
		   3. We consider the answer to be a replay or valid depending on the status directly
		  */
		  if (!preg_match("/otp=".$params['otp']."/", $str) ||
		      !preg_match("/nonce=".$params['nonce']."/", $str)) {
		    /* Case 1. Answer is neither valid or a replay. */
		    $this->_response .= " OTP or nonce mismatch. status=" . $status;
		  } 
		  elseif($this->_key <> "") {
		    /* Case 2. Verify signature first */
		    $rows = split("\r\n", $str);
		    $response=array();
		    while (list($key, $val) = each($rows)) {
		      /* = is also used in BASE64 encoding so we only replace the first = by # which is not used in BASE64 */
		      $val = preg_replace('/=/', '#', $val, 1);
		      $row = split("#", $val);
		      $response[$row[0]] = $row[1];
		    }
		    
		    $parameters=array('nonce','otp', 'sessioncounter', 'sessionuse', 'sl', 'status', 't', 'timeout', 'timestamp');
		    sort($parameters);
		    $check=Null;
		    foreach ($parameters as $param) {
		      if ($response[$param]!=null) {
			if ($check) $check = $check . '&';
			$check = $check . $param . '=' . $response[$param];
		      }
		    }
		    

		    $checksignature = base64_encode(hash_hmac('sha1', utf8_encode($check), $this->_key, true));

		    if($response[h] == $checksignature) {
		      if ($status == 'REPLAYED_OTP') {
			$replay=True;
		      } 
		      if ($status == 'OK') {
			$valid=True;
		      }
		      $this->_response .= " status=" . $status;
		    } else {
		      $this->_response .= " Signature mismatch. status=" . $status ;
		    }
		  } else {
		    /* Case 3. We check the status directly */
		    if ($status == 'REPLAYED_OTP') {
		      $replay=True;
		    } 
		    if ($status == 'OK') {
		      $valid=True;
		    }
		    $this->_response .= " status=" . $status;
		  }
		  $this->_response .= ' for url ' . $cinfo['url'] ."\n";
		}
		if (!$wait_for_all && ($valid || $replay)) 
		  {
		    
		    /* We have a valid answer or a replay, loop should be ended here */
		    foreach ($ch as $h) {
		      curl_multi_remove_handle($mh, $h);
		      curl_close($h);
		    }
		    curl_multi_close($mh);
		    if ($replay) return PEAR::raiseError('REPLAYED_OTP');
		    if ($valid) return true;
		    return PEAR::raiseError($status);
		  }
		
		curl_multi_remove_handle($mh, $info['handle']);
		curl_close($info['handle']);
		unset ($ch[$info['handle']]);
	      }
	      curl_multi_select($mh);
	    }
	  } while ($active);
	  
	  foreach ($ch as $h) {
	    curl_multi_remove_handle ($mh, $h);
	    curl_close ($h);
	  }
	  curl_multi_close ($mh);
	  
	  if ($replay) return PEAR::raiseError('REPLAYED_OTP');
	  if ($valid) return true;
	  return PEAR::raiseError('NO_VALID_ANSWER');
	}
}
?>
