<?php
require_once 'Auth/Yubico.php';
include "config.php";

$username = $_REQUEST["username"];
$password = $_REQUEST["password"];
$mode = $_REQUEST["mode"];
$key = $_REQUEST["key"];
$passwordkey = $_REQUEST["passwordkey"];
$token_size = 32;
$identity_size = 12;

# Quit early on no input
if (!$key && !$passwordkey) {
  $authenticated = -1;
  return;
 }

# Convert passwordkey fields into password + key variables
if ($passwordkey) {
  # Password + key case
  if (strlen ($passwordkey) <= $token_size + $identity_size) {
    $authenticated = 31;
    return;
  }

  $loginkey = substr ($passwordkey, -$token_size);
  $identity = substr ($passwordkey, -$token_size - $identity_size, - $token_size);
  $password = substr ($passwordkey, 0, - $token_size - $identity_size);
  $key = $identity . $loginkey;
 } else if ($mode == "admin") {
  $identity = substr ($key, 0, -$token_size);
 } else {
  $identity = substr ($key, -$token_size - $identity_size, - $token_size);
 }

# Check OTP
$yubi = &new Auth_Yubico($CFG[__CLIENT_ID__], $CFG[__CLIENT_KEY__]);
$auth = $yubi->verify($key);
if (PEAR::isError($auth)) {
  $authenticated = 1;
  return;
 } else {
  $authenticated = 0;
 }

# Fetch realname
$dbconn = pg_connect($CFG[__PGDB__])
  or error_log('Could not connect: ' . pg_last_error());
if (!$dbconn) {
  $authenticated = 2;
  return;
 }

# Admin mode doesn't need realname or username/password-checking
if ($mode == "admin") {
  return;
 }

$query  = "SELECT username FROM demoserver WHERE id='$identity';";
$result = pg_query($query);
if ($result) {
  $row = pg_fetch_row($result);
  if ($row[0]) {
    $realname = $row[0];
  }
 }

# Check password (two-factor)
if ($passwordkey) {
  $query  = "SELECT password FROM demoserver WHERE id='$identity';";
  $result = pg_query($query);
  if ($result) {
    $row = pg_fetch_row($result);
    if ($row[0]) {
      $db_password = $row[0];
    }
  }

  if ($db_password == $password) {
    $authenticated = 0;
  } else {
    $authenticated = 4;
    return;
  }
 }

# Check username (two-factor legacy)
if ($mode == "legacy") {
  if ($realname == $username) {
    $authenticated = 0;
  } else {
    $authenticated = 5;
    return;
  }
 }
?>
