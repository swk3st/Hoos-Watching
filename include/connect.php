<?php

require_once("debug.php");
require_once("secret.php");

define("USERNAME", "pwt5ca");
// define("PASSWORD", ""); // Secret
define("HOST", "usersrv01.cs.virginia.edu");
define("DB_NAME", "pwt5ca");

// Connect to the database.
// From https://www.php.net/manual/en/mysqli.query.php
$db = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);

if ($db->connect_errno) {
    printf("Database connection failed: %s\n", $mysqli->connect_error);
    die();
}