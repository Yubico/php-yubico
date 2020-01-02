<?php
$parts = explode("/", $_SERVER['PATH_INFO']);
http_response_code($parts[1]);
echo $parts[2];
?>
