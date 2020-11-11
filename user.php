<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

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

// Require login?
if (!$user->is_logged_in()) {
    header("Location: ./require_login.php");
    die();
}

// Grab global user; reinit as read only, publicly-viewed user if $user is not the currently viewed user's page.
global $user;
$current_user = null;
if ($email != $user->get_email()) {
    $current_user = new User($email);
} else {
    $current_user = $user;
}

$HEADER_INFO = array(
    "Hoo's Watching | " . $email,
    $email . " <small class='text-muted'> <a href=\"./index.php\">Hoo's Watching</a></small> ",
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <h1>Viewing <?php echo $current_user->get_email(); ?></h1>
</div>

<?php if ($current_user->get_email() == $user->get_email()) : ?>
    <div class="container">
        <h2>Your Watch List</h2>

        <?php
        foreach ($current_user->get_watch_list() as $title) :
        ?>
            <p><?php echo json_encode($title); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="container">
    <h2>Favorites</h2>

    <?php foreach ($current_user->get_favorites_list() as $title) : ?>
        <p><?php echo json_encode($title); ?></p>
    <?php endforeach; ?>
</div>

<?php if ($current_user->get_email() == $user->get_email()) : ?>
    <div class="container">
        <h2>Your Rated Movies</h2>

        <?php
        foreach ($current_user->get_rated_movies() as $title) :
        ?>
            <p><?php echo json_encode($title); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include("include/boilerplate/tail.php"); ?>