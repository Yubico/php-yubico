<html>
  <head>
    <title>Auth_Yubico Demo Page</title>
  </head>
<body>

  <h1>Auth_Yubico Demo Page</h1>

  <p>For more information, please use the following resources:

   <ul>
     <li><a href="https://developers.yubico.com/php-yubico/">
         Homepage of php-yubico</a>

     <li><a href="https://upgrade.yubico.com/getapikey/">
         Yubico API Key Generator</a>
   </ul>

   <h2>Input Parameters</h2>

  <form>

  <table border=1>

<?php

   # Warning!  Supporting user specified URLs is a bad idea if you
   # place this script on the public Internet.  Set $ask_url to 1 on
   # the next line to make it work, for local testing purposes only!

   $ask_url = 0;

   $url = htmlspecialchars($_REQUEST["url"]);
   $sl = htmlspecialchars($_REQUEST["sl"]);
   $timeout = htmlspecialchars($_REQUEST["timeout"]);
   $id = htmlspecialchars($_REQUEST["id"]);
   $key = htmlspecialchars($_REQUEST["key"]);
   $otp = htmlspecialchars($_REQUEST["otp"]);
   $httpsverify = htmlspecialchars($_REQUEST["httpsverify"]);
   $wait_for_all = htmlspecialchars($_REQUEST["wait_for_all"]);

   if ($ask_url == 0 || !$url) {
     $url = "api.yubico.com/wsapi/2.0/verify,api2.yubico.com/wsapi/2.0/verify,api3.yubico.com/wsapi/2.0/verify,api4.yubico.com/wsapi/2.0/verify,api5.yubico.com/wsapi/2.0/verify";
    }
   if (!$id || !$otp) { $key = "oBVbNt7IZehZGR99rvq8d6RZ1DM="; }
   if (!$id) { $id = "1851"; }
   if (!$otp) { $otp = "dteffujehknhfjbrjnlnldnhcujvddbikngjrtgh"; }
   if (!$sl && $sl != 0) { $sl = ""; }
   if (!$timeout && $timeout != 0) { $timeout = ""; }
?>

   <tr>
     <td><b>URL part list (comma separated):</b></td>
     <td><input type=text name=url size=50 value="<?php print $url; ?>" <?php if ($ask_url == 0) { print " readonly"; } ?>></td>
   </tr>

   <tr>
     <td><b>Sync level (0-100) [%] (optional):</b></td>
     <td><input type=text name=sl size=10 value="<?php print $sl; ?>"></td>
   </tr>

   <tr>
     <td><b>timeout [s] (optional):</b></td>
     <td><input type=text name=timeout size=10 value="<?php print $timeout; ?>"></td>
   </tr>

   <tr>
     <td><b>Client ID:</b></td>
     <td><input type=text name=id size=10 value="<?php print $id; ?>"></td>
   </tr>

   <tr>
     <td><b>Key (base64):</b></td>
     <td><input type=text name=key size=30 value="<?php print $key; ?>"></td>
   </tr>

   <tr>
     <td><b>Disable certificate verification:</b></td>
     <td><input type=checkbox name=httpsverify value=1 <?php if ($httpsverify) { print "checked"; } ?>></td>
   </tr>

   <tr>
     <td><b>Wait for all:</b></td>
     <td><input type=checkbox name=wait_for_all value=1 <?php if ($wait_for_all) { print "checked"; } ?>></td>
   </tr>

   <tr>
     <td><b>OTP:</b></td>
     <td><input type=text name=otp size=30 value="<?php print $otp; ?>"></td>
   </tr>

   <tr>
     <td colspan=2><input type=submit></td>
   </tr>

   </table>

  </form>

<?php
   require_once 'Auth/Yubico.php';
   $yubi = new Auth_Yubico($id, $key, null, $httpsverify);
   if ($ask_url) {
      $urls=explode(",", $url);
      foreach($urls as $u) $yubi->addURLpart($u);
    }
   $auth = $yubi->verify($otp, false, $wait_for_all, $sl, $timeout);
?>

  <h2>Last Client Query</h2>

   <pre>
<?php print str_replace (" ", "\n", $yubi->getLastQuery() . " "); ?>
   </pre>

  <h2>Server Responses</h2>

   <pre>
<?php print $yubi->getLastResponse(); ?>
  </pre>

<?php
if (PEAR::isError($auth)) {
  ?><h2>Authentication Failed!</h2>
  <p>Error message: <?php print $auth->getMessage(); ?></p><?php
} else {
  ?><h2>Authenticated Success!</h2><?php
}
?>

</body>
</html>
