<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Services</title>
	<meta http-equiv="Content-Type" content="text/html">
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body onLoad="document.login.username.focus();">
<div class="container">
	<div class="top">&nbsp;</div>
	<img src="logo.jpg" alt="" /><br /><br />

<?php include 'authenticate.php';
if ($authenticated == 0) { ?>
	<h1 class="ok">Congratulations <?php if ($realname) { print "$realname!"; }?></h1>
	<p>You have been successfully authenticated with the YubiKey.
<?php } else { ?>
	<b>Legacy login: Username/password + YubiKey</b><br />
	<ol>
	<li>Place your YubiKey in the USB-port.</li>
	<li>Enter Username in the username field.</li>
	<li>Enter password <b>followed by a colon ":"</b> in the next field.
		Don't press enter or tab after the password.</li>
	<li>Put your finger on the YubiKey button and hold it
	  steady until the YubiKey field is filled.</li>
	</ol>
	<p>No password? You can <a href="admin.php">set password</a> directly.
	<br /><br />

<?php if ($authenticated > 0) { ?>
		<h1 class="fail">Login failure. Please try again. </h1>
<?php } ?>

	<form name="login" method="post" style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px;">
	<input type="hidden" name="mode" value="legacy">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="150">
					<b>Username</b>
			</td>
			<td width="470">
				  <input autocomplete="off" type="text" name="username">
			</td>
		</tr>
		<tr>
			<td width="150">
					<b>Password + YubiKey</b>
			</td>
			<td width="470">
				  <input autocomplete="off" type="password" name="passwordkey"><input type="submit" value="Go" style="border: 0px; font-size: 0px; background: none; padding: 0px; margin: 0px; width: 0px; height: 0px;" />
			</td>
		</tr>
	</table>
	
	</form>

<?php } ?>

	<br /><br />
	<p>» <a href="two_factor_legacy.php">Try again</a>
	<p>» <a href="./">Back to main page</a><br /><br />
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
