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

  <form method=post>

  <table border=1>

<?php
   $id = $_REQUEST["id"];
   $key = $_REQUEST["key"];
   $otp = $_REQUEST["otp"];

   if (!$id || !$otp) {
     $key = "oBVbNt7IZehZGR99rvq8d6RZ1DM=";
   }
   if (!$id) {
     $id = "1851";
   }
   if (!$otp) {
     $otp = "dteffujehknhfjbrjnlnldnhcujvddbikngjrtgh";
   }
?>

   <tr>
   <td><b>Client ID:</b></td>
   <td><input type=text name=id value="<?php print $id; ?>"></td>
   </tr>

   <tr>
   <td><b>Key:</b></td>
   <td><input type=text name=key size=30 value="<?php print $key; ?>"></td>
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
require './Yubico.php';
$yubi = &new Auth_Yubico($id, $key);
$auth = $yubi->verify($otp);
?>

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
