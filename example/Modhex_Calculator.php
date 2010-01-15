<?php
/*
 * Created on May 25, 2009
 *
 */
require_once 'Modhex.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate">
	<title></title>
	<link rel="stylesheet" type="text/css" href="style.css" />
		
	<style type="text/css">
	<!--
	-->
	</style>
</head>
<body onLoad="document.login.srctext.focus();">

	  <div id="stripe">
	  &nbsp;
	  </div>
	  	
	  <div id="container">
	  
		<div id="logoArea">
           <img src="yubicoLogo.gif" alt="yubicoLogo" width="150" height="75"/>
		</div>
		
		<div id="greenBarContent">
			<div id="greenBarImage">
				<img src="yubikey.jpg" alt="yubikey" width="150" height="89"/>
			</div>
			<div id="greenBarText">
				<h3>
					Modhex Calculator
				</h3>
			</div>
		</div>
		<div id="bottomContent">		
<?php
$srctext = $_REQUEST["srctext"];
$srcfmt = $_REQUEST["srcfmt"];

if (strlen($srctext) > 0) {
	
	if($srcfmt == "O") {
		$srctext = substr($srctext, 0, 12);
		$srcfmt = "M";
	}
	
	$b64txt = $srctext;
	if($srcfmt == "P") {
		$b64txt = base64_encode($srctext);
	} else if ($srcfmt == "H") {
		$decval = $srctext;
		$b64txt = hexToB64($decval);
		//echo 'Test B64 : '.$b64txt.' :: '.$decval;
	} else if ($srcfmt == "M") {
		$b64txt = modhexToB64($srctext);
	} else if ($srcfmt == "N") {
		$numval = intval($srctext);
		$decval = dechex($numval);
		//$padcount = strlen($decval) % 8; 
		//for ($j = 0; $j < $padcount; $j++) {
		//	$decval = '0'.$decval;
		//}
		//echo 'Test Val : '.$numval.' :: '.$decval;
		$b64txt = hexToB64($decval);
		//echo 'Test B64 : '.$b64txt;
	} 
	
	$plaintxt = $srctext;
	//$devId_b64 = modhexToB64($devId);
	echo '<h2>Your source string is:</h2><ul>'.
		'<li>Plain text: ' . base64_decode($b64txt) .
		'<li>Number: ' . strval(hexdec(b64ToHex($b64txt))) .
		'<li>Modhex encoded: ' . b64ToModhex($b64txt) .
		'<li>Base64 encoded: ' . $b64txt .
		'<li>Hex encoded: ' . b64ToHex($b64txt) . 
		"</ul>";
}
?>
<p><br>

<form action=Modhex_Calculator.php method=post autocomplete=off>
	<b>Source format:</b><BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="P">Plain text<BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="N">Number<BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="B">Base64<BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="H">Hex<BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="O" CHECKED>OTP<BR>
	<INPUT TYPE=RADIO NAME="srcfmt" VALUE="M">Modhex&nbsp;<BR><BR>
	<b>String:</b>&nbsp;
	<input name=srctext value="" size=50 maxlength=50><p>
	<input type=submit value="Convert to all formats">
</form>
</div>
</body>
</html>
