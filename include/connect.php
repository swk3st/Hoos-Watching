<?php

define("USERNAME", "pwt5ca");
define("PASSWORD", "");
define("HOST", "usersrv01.cs.virginia.edu");
define("DB_NAME", "pwt5ca");

$dsn = "mysql:host=" . HOST . ";dbname=" . DB_NAME;
$db = null;

/** connect to the database **/
try {
    $db = new PDO($dsn, $username, $password);
    echo "<p>You are connected to the database</p>";
} catch (PDOException $e)     // handle a PDO exception (errors thrown by the PDO library)
{
    // Call a method from any object, 
    // use the object's name followed by -> and then method's name
    // All exception objects provide a getMessage() method that returns the error message 
    $error_message = $e->getMessage();
    echo "<p>An error occurred while connecting to the database: $error_message </p>";
} catch (Exception $e)       // handle any type of exception
{
    $error_message = $e->getMessage();
    echo "<p>Error message: $error_message </p>";
}
