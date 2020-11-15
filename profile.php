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

$HEADER_INFO = array(
    "Hoo's Watching | " . $current_user->get_email(),
    "Profile",
    $current_user->get_email()
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <div class="main-body">
      <link rel="stylesheet" href="assets/css/profile.css">
    
          <div class="row gutters-sm">
            <div class="col-md-4 mb-3">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex flex-column align-items-center text-center">
                    <img src="assets/img/noun_person_124296.png" alt="Profile photo" class="rounded-circle" width="150">
                    <div class="mt-3">
                      <h4><?php echo $current_user->get_email(); ?></h4>
                      <!-- <p class="text-secondary mb-1">Full Stack Developer</p>
                      <p class="text-muted font-size-sm">Bay Area, San Francisco, CA</p> -->
                      <form action="./friends.php" method="post">
                        <input type="hidden" name="friends_add_email" value="<?php $current_user->get_email() ?>">
                        <button class="btn btn-primary mt-4" <?php echo $current_user_is_self ? "disabled" : "" ?>>Add as friend</button>
                      </form>
                      <!-- <button class="btn btn-outline-primary">Message</button> -->
                    </div>
                  </div>
                </div>
              </div>
              <div class="card mt-3">
              </div>
            </div>
            <div class="col-md-8">
              <div class="card mb-3">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Email</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                    <?php echo $current_user->get_email(); ?>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">User since</h6>
                    </div>
                    <div class="col-sm-9 text-secondary">
                    <?php echo $current_user->get_creation_date(); ?>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Number of friends</h6>
                    </div>
                    <div class="col-sm-6 text-secondary">
                      <?php echo $current_user->friends_get_count(); ?>
                    </div>
                    <div class="col-sm-3 text-secondary text-md-right">
                      <a href="./friends.php?email=<?php echo $current_user->get_email(); ?>">View friends</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Favorited movies</h6>
                    </div>
                    <div class="col-sm-6 text-secondary">
                      <?php echo $current_user->movie_count_favorites(); ?>
                    </div>
                    <div class="col-sm-3 text-secondary text-md-right">
                      <a href="./favorites.php?email=<?php echo $current_user->get_email(); ?>">View favorites</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Movies on watch list</h6>
                    </div>
                    <div class="col-sm-6 text-secondary">
                      <?php echo $current_user->movie_count_watch_list(); ?>
                    </div>
                    <div class="col-sm-3 text-secondary text-md-right">
                      <a href="./watchlist.php?email=<?php echo $current_user->get_email(); ?>">View watchlist</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Rated movies</h6>
                    </div>
                    <div class="col-sm-6 text-secondary">
                    <?php echo $current_user->movie_count_rated(); ?>
                    </div>
                    <div class="col-sm-3 text-secondary text-md-right">
                      <a href="./rated_titles.php?email=<?php echo $current_user->get_email(); ?>">View rated movies</a>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <h6 class="mb-0">Favorite people</h6>
                    </div>
                    <div class="col-sm-6 text-secondary">
                    <?php echo $current_user->name_get_favorites_count(); ?>
                    </div>
                    <div class="col-sm-3 text-secondary text-md-right">
                      <a href="./favoritepeople.php?email=<?php echo $current_user->get_email(); ?>">View favorite people</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

<?php include("include/boilerplate/tail.php"); ?>