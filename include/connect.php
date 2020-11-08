<?php

define("DEBUG", true);

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function debug_echo($value)
{
    if (!DEBUG) {
        return;
    }
    $s = strval($value);
    echo "<div class=\"container border rounded border-danger mt-3 mb-3 p-3\"> $s </div>";
}

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
