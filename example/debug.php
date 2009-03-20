<?php
	print '<p><table  style="border: 1px solid #e5e5e5; background-color: #f1f1f1; padding: 10px; margin: 0px;" width="100%">';

	print "<tr><td colspan=2><b>HTML fields</b></td></tr>\n";
	print "<tr><td width=70><b>mode</b></td><td>";
	if ($username && $password) {
	    print "two-factor legacy";
        } else if ($password) {
	    print "two-factor";
	} else {
	    print "one-factor";
	}
	print "</td></tr>\n";
	!$key or
	 print "<tr><td width=70><b>key</b></td><td>$key</td></tr>\n";
	!$passwordkey or
	 print "<tr><td width=70><b>passwordkey</b></td><td>$passwordkey</td></tr>\n";
	!$username or
	 print "<tr><td width=70><b>username</b></td><td>$username</td></tr>\n";
	!$password or
	 print "<tr><td width=70><b>password</b></td><td>$password</td></tr>\n";
	!$identity or
	 print "<tr><td width=70><b>identity</b></td><td>$identity</td></tr>\n";
	!$realname or
	 print "<tr><td width=70><b>realname</b></td><td>$realname</td></tr>\n";
	print "<tr><td colspan=2>&nbsp;</td></tr>\n";
	print "<tr><td colspan=2><b>Authentication Output</b></td></tr>\n";
	if ($yubi) {
	   $txt = $yubi->getLastResponse();
	}
	$txt = preg_replace ('/\n/', '<br>', $txt);
	print "<tr><td colspan=2>$txt</td></tr>\n";
	print "</table></p>\n";
?>
