<html>
  <head>
    <title>Auth_Yubico Demo Page</title>
  </head>
<body>

  <h1>Auth_Yubico Demo Page</h1>

  <p>For more information, please use the following resources:

   <ul>
     <li><a href="http://code.google.com/p/php-yubico/">
         Auth_Yubico Homepage</a>

     <li><a href="http://yubico.com/developers/api/">
         Yubico API documentation</a>

     <li><a href="http://api.yubico.com/get-api-key/">
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

   $url = $_REQUEST["url"];
   $id = $_REQUEST["id"];
   $key = $_REQUEST["key"];
   $otp = $_REQUEST["otp"];
   $https = $_REQUEST["https"];

   if (!$id || !$otp) {
     $key = "oBVbNt7IZehZGR99rvq8d6RZ1DM=";
   }
   if (!$url) {
     $url = "api.yubico.com/wsapi/verify";
   }
   if (!$id) {
     $id = "1851";
   }
   if (!$otp) {
     $otp = "dteffujehknhfjbrjnlnldnhcujvddbikngjrtgh";
   }

   if ($ask_url) { ?>

   <tr>
   <td><b>URL part:</b></td>
   <td><input type=text name=url value="<?php print $url; ?>"></td>
   </tr>

<?php } ?>

   <tr>
   <td><b>Client ID:</b></td>
   <td><input type=text name=id value="<?php print $id; ?>"></td>
   </tr>

   <tr>
   <td><b>Key (base64):</b></td>
   <td><input type=text name=key size=30 value="<?php print $key; ?>"></td>
   </tr>

   <tr>
   <td><b>OTP:</b></td>
   <td><input type=text name=otp size=30 value="<?php print $otp; ?>"></td>
   </tr>

   <tr>
   <td><b>Use HTTPS:</b></td>
   <td><input type=checkbox name=https value=1 <?php if ($https) { print "checked"; } ?>></td>
   </tr>

   <tr>
   <td colspan=2><input type=submit></td>
   </tr>

   </table>

  </form>

<?php
    require_once 'Auth/Yubico.php';
    $yubi = &new Auth_Yubico($id, $key, $https);
    if ($ask_url) {
      $yubi->setURLpart($url);
    }
    $auth = $yubi->verify($otp);
?>

  <h2>Client Query</h2>

<pre><?php print $yubi->getLastQuery(); ?></pre>

  <h2>Server Response</h2>

<pre><?php print $yubi->getLastResponse(); ?></pre>

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
