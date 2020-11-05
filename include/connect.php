<?php

define("USERNAME", "pwt5ca");
define("PASSWORD", "ilebHF1wf4OtsvpGvkb!");
define("HOST", "usersrv01.cs.virginia.edu");
define("DB_NAME", "pwt5ca");

$dsn = "mysql:host=" . HOST . ";dbname=" . DB_NAME;

/** connect to the database **/
// From https://www.php.net/manual/en/mysqli.query.php
$db = new mysqli(HOST, USERNAME, PASSWORD, DB_NAME);
// echo "<p>You are connected to the database</p>";

if ($db->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    die();
}
