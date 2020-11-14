<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/name-funcs.php");
require_once("include/util.php");
require_once("include/images.php");

$nconst = null;
$person = null;
$roles = null;
if (isset($_GET['nconst'])) {
    $nconst = $_GET['nconst'];
    $person = name_get_info($_GET['nconst']);
    $roles = name_get_roles($_GET['nconst']);
}

// Redirect back to the home page if the title isn't valid.
if (is_null($nconst)) {
    header("Location: ./index.php");
    die();
}

global $user;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addFavoriteNconst'])) {
        global $MESSAGE;
        if ($user->name_add_favorite($_POST['addFavoriteNconst'])) {
            $MESSAGE = "Added person to favorites!";
        } else {
            $MESSAGE = "Failed to add person to favorites.";
        }
    } else if (isset($_POST['removeFavoriteNconst'])) {
        global $MESSAGE;
        if ($user->name_remove_favorite($_POST['removeFavoriteNconst'])) {
            $MESSAGE = "Removed person from favorites!";
        } else {
            $MESSAGE = "Failed to remove person from favorites.";
        }
    } else if (isset($_POST['rateNconst']) && isset($_POST['rateStars'])) {
        global $MESSAGE;
        if ($user->name_add_rating($_POST['rateNconst'], (int) $_POST['rateStars'])) {
            $MESSAGE = "Successfully rated person!";
        } else {
            $MESSAGE = "Failed to rate person.";
        }
    } else if (isset($_POST['removeRatingNconst'])) {
        global $MESSAGE;
        if ($user->name_remove_rating($_POST['removeRatingNconst'])) {
            $MESSAGE = "Successfully removed rating for person!";
        } else {
            $MESSAGE = "Failed to remove rating for person.";
        }
    }
}


$HEADER_INFO = array(
    "Hoo's Watching | " . $person['primaryName'],
    "Hoo's Watching",
    $person['primaryName']
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <div class="row">
        <div class="col-sm-3 mb-5">
            <div>
                <!-- Photo goes here... -->
                <img src="assets/img/noun_person_124296.png" alt="" class="w-100" 1>
            </div>
            <div class="text-sm-center">
                <?php
                global $user;
                global $nconst;
                if ($user->name_is_favorite($nconst)) :
                ?>
                    <form action="" method="post">
                        <input type="hidden" name="removeFavoriteNconst" value="<?php echo $nconst; ?>">
                        <button class="w-75 btn btn-danger btn-sm mt-2">Remove from favorites</button>
                    </form>
                <?php else : ?>
                    <form action="" method="post">
                        <input type="hidden" name="addFavoriteNconst" value="<?php echo $nconst; ?>">
                        <button class="w-75 btn btn-primary btn-sm mt-2">Add to favorites</button>
                    </form>
                <?php endif ?>
            </div>
            <div class="text-sm w-100 mt-4">
                <strong>Rate this person</strong>
                <form action="" method="post">
                    <div class="btn-group w-100 mx-auto" role="group" aria-label="Star rating">
                        <?php global $user; ?>
                        <?php $rating = $user->name_get_rating($nconst); ?>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 1 ? "active" : ""; ?>" name="rateStars" value="1">1 <i class="fa fa-star"></i></button>
                        <input type="hidden" name="rateNconst" value="<?php echo $nconst; ?>">
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 2 ? "active" : ""; ?>" name="rateStars" value="2">2 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 3 ? "active" : ""; ?>" name="rateStars" value="3">3 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 4 ? "active" : ""; ?>" name="rateStars" value="4">4 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 5 ? "active" : ""; ?>" name="rateStars" value="5">5 <i class="fa fa-star"></i></button>
                        <?php if ($rating) : ?>
                            <button type="submit" class="btn btn-danger btn-sm" name="removeRatingNconst" value="<?php echo $nconst; ?>"><i class="fa fa-times"></i></button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-9">
            <table class="table table-striped">
                <!-- $tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes -->
                <thead>
                    <tr>
                        <th scope="col">Type</th>
                        <th scope="col">Person Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Name</th>
                        <td><?php echo $person['primaryName']; ?></td>
                    </tr>
                    <?php if ($person['birthYear']) : ?>
                        <tr>
                            <th scope="row">Birth Year</th>
                            <td><?php echo $person['birthYear']; ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($person['deathYear']) : ?>
                        <tr>
                            <th scope="row">Death Year</th>
                            <td><?php echo $person['deathYear']; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container">
    <h3>Roles</h3>
    <table class="table table-striped">
        <thead>
            <th>Title</th>
            <th>Title Type</th>
            <th>Year</th>
            <th>Role</th>
            <th>Characters</th>
        </thead>
        <tbody>
            <?php foreach ($roles as $role) : ?>
                <tr>
                    <th><a href="title.php?tconst=<?php echo $role['tconst']; ?>"><?php echo $role['primaryTitle']; ?></a></th>
                    <td><?php echo $role['titleType']; ?></td>
                    <td><?php echo $role['startYear']; ?></td>
                    <td><?php echo $role['category']; ?></td>
                    <td><?php echo $role['characters'] ? join(", ", $role['characters']) : ""; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("include/boilerplate/tail.php"); ?>