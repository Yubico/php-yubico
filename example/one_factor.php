<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Services</title>
	<meta http-equiv="Content-Type" content="text/html">
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>

<body onLoad="document.login.key.focus();">
<div class="container">
	<div class="top">&nbsp;</div>
	<img src="logo.jpg" alt="" /><br /><br />

<?php include 'authenticate.php';
if ($authenticated == 0) { ?>
	<h1 class="ok">Congratulations <?php if ($realname) { print "$realname!"; }?></h1>
	<p>You have been successfully authenticated with the YubiKey.
<?php } else { ?>
	<b>One factor: YubiKey only</b><br />
	<ol>
	<li>Place your YubiKey in the USB-port.</li>
	<li>Put your finger on the YubiKey button and hold it
	  steady until the YubiKey field is filled.</li>
	</ol>
	<br />

<?php if ($authenticated > 0) { ?>
		<h1 class="fail">Login failure. Please try again. </h1>
<?php } ?>

	<form name="login" method="get" style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px;">
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
	<p>» <a href="one_factor.php">Try again</a>
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
