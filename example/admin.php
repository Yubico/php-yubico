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
if ($authenticated == 0) {

  $query  = "DELETE FROM demoserver WHERE id='$identity'";
  pg_query($query) or die('Error, admin delete failed: ' . pg_error());
  $query  = "INSERT INTO demoserver (id, username, password) values ('$identity', '$username', '$password');";
  pg_query($query) or die('Error, admin insert failed: ' . pg_error());
 ?>

		<h1 class="ok">Congratulations <?php if ($realname) { print "$realname!"; }?></h1>
		You have successfully set the username/password to use with the Demo server.
		<p>» <a href="./">Continue to main page&gt;&gt;</a>
<?php } else { ?>

<b>Set username and password for Yubico Demo server</b><br>
	<ol>
		<li>Place your YubiKey in the USB-port.</li>
		<li>Enter Username and Password.</li>
		<li>Put your finger on the YubiKey button and hold it
		  steady until the YubiKey field is filled.</li>
	</ol>
<br>

<?php if ($authenticated > 0) { ?>
<h1 class="fail">
Authentication failure. Please try again. </h1><br>
<?php } ?>


		<form name="login" method="get" style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px;">
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
						<b>Password</b>
				</td>
				<td width="470">
					  <input autocomplete="off" type="password" name="password">
				</td>
			</tr>
			<tr>
				<td width="150">
						<b>YubiKey</b>
				</td>
				<td width="470">
					 <input autocomplete="off" type="password" name="key">
					 <input type="hidden" name="mode" value="admin"><input type="submit" value="Go" style="border: 0px; font-size: 0px; background: none; padding: 0px; margin: 0px; width: 0px; height: 0px;" />
				</td>
			</tr>
		</table>
	
	</form>

<p>» <a href="./">Back to main page</a>

<?php } ?>

</div>
</body>
</html>
