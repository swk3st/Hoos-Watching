<?php

require_once("include/db_interface.php");
require_once("include/user-funcs.php");

$email = null;
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
    }
}

// Redirect back to the home page if the index isn't valid.
if (is_null($email)) {
    header("Location: ./index.php");
    die();
}

$HEADER_INFO = array(
    "Hoo's Watching | " . $email,
    $title['primaryTitle'] . " <small class='text-muted'> <a href=\"./index.php\">Hoo's Watching</a></small> ",
    "Hoo's Watching | " . $email
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <?php
    echo $email;
    ?>
</div>

<?php include("include/boilerplate/tail.php"); ?>