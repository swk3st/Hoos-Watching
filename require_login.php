<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("include/db_interface.php");
require_once("include/user-funcs.php");

global $user;
// Redirect back to the home page if the user is actually logged in.
if ($user->is_logged_in()) {
    header("Location: ./index.php");
    die();
}

$HEADER_INFO = array(
    "Hoo's Watching | Login Required",
    "Hoo's Watching",
    "Login Required",
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <h3>You need to be logged in to see this content.</h3>
    <p>You can <a href="./sign_in.php">log in here</a>, or you can <a href="./sign_up.php">make an account here.</a></p>
</div>

<?php include("include/boilerplate/tail.php"); ?>