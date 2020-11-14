<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("include/db_interface.php");
require_once("include/user-funcs.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle logout request.
    if (isset($_POST['logout']) && $_POST['logout'] == "1") {
        session_unset();
        global $user;
        $user = new User();
    }
}

$HEADER_INFO = array(
    "Hoo's Watching | Logged out",
    "Hoo's Watching",
    "Logged Out",
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <h3>You have been logged out.</h3>
    <p>You can <a href="./sign_in.php">log in here</a>, or you can <a href="./sign_up.php">make an account here.</a></p>
</div>

<?php include("include/boilerplate/tail.php"); ?>