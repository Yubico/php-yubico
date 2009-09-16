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

<body onLoad="document.login.key.focus();">

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
					One factor: YubiKey only
				</h3>
			</div>
		</div>
		<div id="bottomContent">
<?php include 'authenticate.php';
if ($authenticated == 0) { ?>
	<h1 class="ok">Congratulations <?php if ($realname) { print "$realname!"; }?></h1>
	<p>You have been successfully authenticated with the YubiKey.
<?php } else { ?>
	<ol style="list-style-position: outside;">
	<li>Place your YubiKey in the USB-port.</li>
	<li>Put your finger on the YubiKey button and hold it
	  steady until the YubiKey field is filled.</li>
	</ol>
	<br />

<?php if ($authenticated > 0) { ?>
		<h1 class="fel">Login failure. Please try again. </h1>
<?php } ?>

	<form name="login" method="get" style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px; font-size:12px;">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td>
					<b>YubiKey</b>
			</td>
			<td>
				  <input autocomplete="off" type="password" name="key">
			</td>
		</tr>
	</table>
	</form>

<?php } ?>

	<br /><br />
	<p>&raquo; <a href="one_factor.php">Try again</a>
	<p>&raquo; <a href="./">Back to main page</a><br /><br />
	<br /><br /><br /><br />

<?php if ($authenticated >= 0) { ?>
	<h3>Technical details</h3>
	More information about the performed transcaction:
	<br /><br />
	<?php include 'debug.php';
} ?>

</div>
</body>
</html>
