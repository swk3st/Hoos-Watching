<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");

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
  if (isset($_POST['remove_title_tconst'])) {
    $tconst = $_POST['remove_title_tconst'];
    if ($current_user->movie_remove_from_favorites($tconst)) {
      global $MESSAGE;
      $MESSAGE = "Successfully removed title from favorites.";
    } else {
      global $MESSAGE;
      $MESSAGE = "Failed to remove title from favorites.";
    }
  }
}

$HEADER_INFO = array(
  "Hoo's Watching | Favorites",
  "Favorites",
  $current_user->get_email() . "'s favorite titles"
);
include("include/boilerplate/head.php");
?>


<!-- <link href="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1557937989/lib.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1557937989/lib.js"></script> -->
<div class="container" id="page-content">
  <link rel="stylesheet" href="assets/css/watchlist.css">
  <div class="padding">
    <div class="row container d-flex justify-content-center">
      <div class="stretch-card">
        <div class="card border rounded">
          <div class="card-body pb-0">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <!-- tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes -->
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Year</th>
                    <th scope="col">Length</th>
                    <th scope="col">IMDb rating (1-10)</th>
                    <th scope="col">HW rating (1-5)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $titles = $current_user->get_favorites_list();
                  if (sizeof($titles) > 0) : ?>
                    <?php foreach ($titles as $title) : ?>
                      <tr>
                        <th>
                          <?php echo $title['favoritesRank']; ?>
                        </th>
                        <td>
                          <a href="./title.php?tconst=<?php echo $title['tconst']; ?>">
                            <?php echo $title['primaryTitle']; ?>
                          </a>
                          <small class="text-muted"><?php echo $title['titleType']; ?></small>
                        </td>
                        <td>
                          <?php
                          echo $title['startYear'];
                          if (!is_null($title['endYear'])) {
                            echo "-" . $title['endYear'];
                          }
                          ?>
                        </td>
                        <td><?php echo minutes_to_human_time($title['runtimeMinutes']); ?></td>
                        <td><?php
                            echo number_format($title['averageRating'], 1) .
                              " (" .
                              number_format($title['numVotes']) .
                              " votes)";
                            ?></td>
                        <td>
                          <?php
                          if ($title['numUserVotes'] > 0) {
                            echo number_format($title['userRating'], 1) .
                              " (" .
                              number_format($title['numUserVotes']) .
                              " votes)";
                          }
                          ?>
                        </td>
                        <td>
                          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" name="remove_title_tconst" value="<?php echo $title['tconst']; ?>">
                            <button type="submit" class="close" aria-label="Remove from favorites">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </form>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php elseif ($current_user_is_self) : ?>
                    <tr>
                      <th scope="row">
                        Get started by adding a movie to your favorites!
                      </th>
                    </tr>
                  <?php else : ?>
                    <tr>
                      <th scope="row">
                        This user has no movies on their favorites yet.
                      </th>
                    </tr>
                  <?php endif; ?>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include("include/boilerplate/tail.php"); ?>