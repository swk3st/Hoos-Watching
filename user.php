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

$HEADER_INFO = array(
    "Hoo's Watching | " . $email,
    $email . " <small class='text-muted'> <a href=\"./index.php\">Hoo's Watching</a></small> ",
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <h1>Viewing <?php echo $email; ?></h1>
</div>

<?php
// Only display watch list info if the user is logged in.
global $user;


$user->movie_add_to_watch_list("tt0111161", 1);
$user->movie_add_to_watch_list("tt0468569", 3);
$user->movie_add_to_watch_list("tt1375666", 2);
$user->movie_add_to_watch_list("tt0137523", 4);

$user->movie_add_to_favorites("tt0111161", 4);
$user->movie_add_to_favorites("tt0468569", 2);
$user->movie_add_to_favorites("tt1375666", 1);
$user->movie_add_to_favorites("tt0137523", 3);

$user->movie_add_rating("tt0111161", 5);
$user->movie_add_rating("tt0468569", 4);
$user->movie_add_rating("tt1375666", 4);
$user->movie_add_rating("tt0137523", 5);


if ($email == $user->get_email()) :
?>
    <div class="container">
        <h2>Watch List</h2>

        <?php
        global $user;
        foreach ($user->get_watch_list() as $title) :
        ?>
            <p><?php echo json_encode($title); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include("include/boilerplate/tail.php"); ?>