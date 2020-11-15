<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("include/db_interface.php");
require_once("include/user-funcs.php");

// Grab global user; reinit as read only, publicly-viewed user if $user is not the currently viewed user's page.
global $user;
$current_user = null;
if (isset($_GET['email'])) {
    $current_user = new User($_GET['email']);
} else if ($user->is_logged_in()) {
    $current_user = $user;
}

// Redirect back to the home page if the index isn't valid.
if (is_null($current_user)) {
    header("Location: ./require_login.php");
    die();
}

// Require login?
if (!$user->is_logged_in()) {
    header("Location: ./require_login.php");
    die();
}

$current_user_is_self = $user->get_email() == $current_user->get_email();

// Do actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['remove_person_nconst']) && $current_user_is_self) {
        $nconst = $_POST['remove_person_nconst'];
        $user->name_remove_favorite($nconst);

        global $MESSAGE;
        $MESSAGE = "Removed " . $nconst . " from your favorites.";
    }
}

$HEADER_INFO = array(
    "Hoo's Watching | Friends",
    "Favorite People",
    "Favorite actors, writers, etc. of " . $current_user->get_email(),
);
include("include/boilerplate/head.php");
?>

<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="content">
    <link rel="stylesheet" href="assets/css/friends.css">
    <div class="container">
        <?php /*
        <?php if ($current_user_is_self) : ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="row mb-3">
                    <!-- Add friends form -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">Add a friend by email</span>
                        </div>
                        <input type="text" class="form-control" placeholder="Friend's email" aria-label="Friend's email" name="friends_add_email">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Add</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
        */ ?>
        <?php
        $people = $current_user->name_get_favorites();
        $people_len = sizeof($people);
        if ($people_len <= 0) :
        ?>
            <div>No favorite people yet!</div>
        <?php else : ?>
            <?php for ($row = 0; $row < $people_len / 3; $row++) : ?>
                <div class="row">
                    <?php for ($i = $row * 3; $i < $people_len && $i < ($row + 1) * 3; $i++) : ?>
                        <div class="col-lg-4">
                            <div class="card bg-light text-center card-box">
                                <div class="member-card pt-2 pb-2">
                                    <div class="thumb-lg member-thumb mx-auto"><img src="assets/img/noun_person_124296.png" class="rounded-circle img-thumbnail" alt="profile-image"></div>
                                    <div class="mt-2">
                                        <h4><?php echo $people[$i]['primaryName']; ?></h4>
                                    </div>
                                    <!-- <button type="button" class="btn btn-primary mt-3 btn-rounded waves-effect w-md waves-light">View Profile</button> -->
                                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4">
                                        <a href="./people.php?nconst=<?php echo $people[$i]['nconst']; ?>" class="btn btn-primary btn-rounded">View Person</a>
                                        <?php if ($current_user_is_self) : ?>
                                            <input type="hidden" name="remove_person_nconst" value="<?php echo $people[$i]['nconst']; ?>">
                                            <button class="btn btn-danger btn-rounded" type="submit">Remove Favorite</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
    <!-- container -->
</div>

<?php include("include/boilerplate/tail.php"); ?>