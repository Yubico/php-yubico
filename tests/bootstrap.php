<?php
/* We need to redirect the webserver's stderr and stdout,
 * otherwise exec() will wait for the webserver to close its
 * stdout before returning in order to collect all its output.
 */

$command = '/usr/bin/env php -S localhost:3961 -t . >/dev/null 2>&1 & echo $!';
$output = array();
exec($command, $output);
$webserver_pid = (int)$output[0];
echo "webserver running as pid " . $webserver_pid . "\n";

/* It appears that PHP kills off its children before we get to this point */
/*
register_shutdown_function(function() use ($webserver_pid) {
    echo "killing webserver (pid " . $webserver_pid . ")\n";
    exec('kill ' . $webserver_pid);
});
*/
