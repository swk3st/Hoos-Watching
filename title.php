<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");
require_once("include/images.php");

$title = null;
// if ($_SERVER["REQUEST_METHOD"] == "GET") {
if (isset($_GET['tconst'])) {
    $title = title_get_info($_GET['tconst']);
}
// }

// Redirect back to the home page if the title isn't valid.
if (is_null($title)) {
    header("Location: ./index.php");
    die();
}

global $user;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['commentTextArea'])) {
        global $user;
        if (!$user->create_comment_on_title($title['tconst'], $_POST['commentTextArea'])) {
            debug_echo("Create comment failed.");
        } else {
            global $MESSAGE;
            $MESSAGE = "Comment posted successfully!";
        }
    } else if (isset($_POST['removeCommentTconst']) && isset($_POST['removeCommentEmail']) && isset($_POST['removeCommentDate']) && $_POST['removeCommentEmail'] == $user->get_email()) {
        global $MESSAGE;
        if ($user->remove_comment_on_title($_POST['removeCommentTconst'], $_POST['removeCommentDate'])) {
            $MESSAGE = "Comment removed successfully.";
        } else {
            $MESSAGE = "Failed to delete comment.";
        }
    } else if (isset($_POST['addFavoriteTconst'])) {
        global $MESSAGE;
        if ($user->movie_add_to_favorites($_POST['addFavoriteTconst'])) {
            $MESSAGE = "Added title to favorites!";
        } else {
            $MESSAGE = "Failed to add title to favorites.";
        }
    } else if (isset($_POST['removeFavoriteTconst'])) {
        global $MESSAGE;
        if ($user->movie_remove_from_favorites($_POST['removeFavoriteTconst'])) {
            $MESSAGE = "Removed title from favorites!";
        } else {
            $MESSAGE = "Failed to remove title from favorites.";
        }
    } else if (isset($_POST['addWatchLaterTconst'])) {
        global $MESSAGE;
        if ($user->movie_add_to_watch_list($_POST['addWatchLaterTconst'])) {
            $MESSAGE = "Added title to your watch list!";
        } else {
            $MESSAGE = "Failed to add title to your watch list.";
        }
    } else if (isset($_POST['removeWatchLaterTconst'])) {
        global $MESSAGE;
        if ($user->movie_remove_from_watch_list($_POST['removeWatchLaterTconst'])) {
            $MESSAGE = "Removed title from your watch list!";
        } else {
            $MESSAGE = "Failed to from title from your watch list.";
        }
    } else if (isset($_POST['rateTconst']) && isset($_POST['rateStars'])) {
        global $MESSAGE;
        if ($user->movie_add_rating($_POST['rateTconst'], (int) $_POST['rateStars'])) {
            $MESSAGE = "Successfully rated title!";
        } else {
            $MESSAGE = "Failed to rate title.";
        }
    } else if (isset($_POST['removeRatingTconst'])) {
        global $MESSAGE;
        if ($user->movie_remove_rating($_POST['removeRatingTconst'])) {
            $MESSAGE = "Successfully removed rating for title!";
        } else {
            $MESSAGE = "Failed to remove rating for title.";
        }
    }
}




$HEADER_INFO = array(
    "Hoo's Watching | " . $title['primaryTitle'],
    "Hoo's Watching",
    $title['primaryTitle']
);
include("include/boilerplate/head.php");
?>

<div class="container">
    <div class="row">
        <div class="col-sm-3">
            <div>
                <?php $poster = get_poster($title['tconst']); ?>
                <img class="w-100" src="<?php echo $poster; ?>" alt="<?php echo $title['primaryTitle']; ?> poster">
            </div>
            <div class="text-sm-center">
                <?php global $user;
                if ($user->movie_is_favorite($title['tconst'])) : ?>
                    <form action="" method="post">
                        <input type="hidden" name="removeFavoriteTconst" value="<?php echo $title['tconst']; ?>">
                        <button class="w-75 btn btn-danger btn-sm mt-2">Remove from favorites</button>
                    </form>
                <?php else : ?>
                    <form action="" method="post">
                        <input type="hidden" name="addFavoriteTconst" value="<?php echo $title['tconst']; ?>">
                        <button class="w-75 btn btn-primary btn-sm mt-2">Add to favorites</button>
                    </form>
                <?php endif ?>
            </div>
            <div class="text-sm-center">
                <?php global $user;
                if ($user->movie_is_on_watch_list($title['tconst'])) : ?>
                    <form action="" method="post">
                        <input type="hidden" name="removeWatchLaterTconst" value="<?php echo $title['tconst']; ?>">
                        <button class="w-75 btn btn-danger btn-sm mt-2">Remove from watch list</button>
                    </form>
                <?php else : ?>
                    <form action="" method="post">
                        <input type="hidden" name="addWatchLaterTconst" value="<?php echo $title['tconst']; ?>">
                        <button class="w-75 btn btn-primary btn-sm mt-2">Add to watch list</button>
                    </form>
                <?php endif ?>
            </div>
            <div class="text-sm w-100">
                <strong class="mt-4">Rate this title</strong>
                <form action="" method="post">
                    <div class="btn-group w-100 mx-auto" role="group" aria-label="Star rating">
                        <input type="hidden" name="rateTconst" value="<?php echo $title['tconst']; ?>">
                        <?php global $user; ?>
                        <?php $rating = $user->movie_get_rating($title['tconst']); ?>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 1 ? "active" : ""; ?>" name="rateStars" value="1">1 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 2 ? "active" : ""; ?>" name="rateStars" value="2">2 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 3 ? "active" : ""; ?>" name="rateStars" value="3">3 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 4 ? "active" : ""; ?>" name="rateStars" value="4">4 <i class="fa fa-star"></i></button>
                        <button type="submit" class="btn btn-primary btn-sm <?php echo $rating == 5 ? "active" : ""; ?>" name="rateStars" value="5">5 <i class="fa fa-star"></i></button>
                        <?php if (!is_null($rating)) : ?>
                            <button type="submit" class="btn btn-danger btn-sm" name="removeRatingTconst" value="<?php echo $title['tconst']; ?>"><i class="fa fa-times"></i></button>
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
                        <th scope="col">Movie Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Primary Title</th>
                        <td><?php echo $title['primaryTitle']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Original Title</th>
                        <td><?php echo $title['originalTitle']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Is Adult?</th>
                        <td><?php echo $title['isAdult'] ? "yes" : "no"; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Start Year</th>
                        <td><?php echo $title['startYear']; ?></td>
                    </tr>
                    <?php if (!is_null($title['endYear'])) : ?>
                        <tr>
                            <th scope="row">End Year</th>
                            <td><?php echo $title['endYear']; ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th scope="row">Length</th>
                        <td><?php echo minutes_to_human_time($title['runtimeMinutes']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Average Rating (IMDb)</th>
                        <td><?php echo number_format($title['averageRating'], 1); ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Number of Votes (IMDb)</th>
                        <td><?php echo number_format($title['numVotes']); ?></td>
                    </tr>
                    <?php if ($title['numUserVotes'] > 0) : ?>
                        <tr>
                            <th scope="row">Average Rating (Hoo's Watching)</th>
                            <td><?php echo number_format($title['userRating'], 1); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Number of Votes (Hoo's Watching)</th>
                            <td><?php echo number_format($title['numUserVotes']); ?></td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <th scope="row">Average Rating (Hoo's Watching)</th>
                            <td>Be the first to vote!</td>
                        </tr>
                        <tr>
                            <th scope="row">Number of Votes (Hoo's Watching)</th>
                            <td>0</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container">
    <h3 class="mt-5">People</h3>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Role</th>
                <th scope="col">Characters</th>
                <th scope="col">Age</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $people = title_get_people($title['tconst']);
            foreach ($people as $person) :
            ?>
                <tr>
                    <th><?php echo $person['primaryName'] ?></th>
                    <td><?php echo $person['category'] ?></td>
                    <td><?php echo $person['characters'] ? join(", ", $person['characters']) : "" ?></td>
                    <td><?php
                        if ($person['birthYear']) {
                            echo (int) ($person['deathYear'] ? (int) $person['deathYear'] : 2020) - $person['birthYear'];
                        } else {
                            echo "";
                        }
                        ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Display all comments for the title. -->
<div class="container">
    <h3 id="comments" class='mt-5'>Comments</h3>

    <?php
    $comments = title_get_comments($title['tconst']);
    if (sizeof($comments) > 0) : ?>
        <ul class="list-unstyled">
            <?php foreach ($comments as $comment) : ?>
                <li class="media m-3 p-3 pb-0 border rounded">
                    <img src="assets/img/noun_person_124296.png" class="mr-3" alt="Profile picture" width=64 height=64>
                    <div class="media-body">
                        <h5 class="mt-0">
                            <a href="./profile.php?email=<?php echo $comment['email']; ?>">
                                <?php echo $comment['email']; ?>
                            </a>
                            <small class="text-muted"> at <?php echo $comment['date_added']; ?></small>
                        </h5>
                        <p><?php echo $comment['text']; ?></p>

                        <?php if ($comment['email'] == $user->get_email()) : ?>
                            <form action="<?php echo $_SERVER['PHP_SELF'] . "?tconst=" . $title['tconst']; ?>" method="post">
                                <input type="hidden" name="removeCommentTconst" value="<?php echo $title['tconst']; ?>">
                                <input type="hidden" name="removeCommentEmail" value="<?php echo $comment['email']; ?>">
                                <input type="hidden" name="removeCommentDate" value="<?php echo $comment['date_added']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete this comment</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <small class="text-muted">person by Diego Naive from the Noun Project</small>
    <?php else : ?>
        <p>
            No comments yet!
            <?php global $user;
            if ($user->is_logged_in()) : ?>
                Be the first to comment.
            <?php endif; ?>
        </p>
    <?php endif; ?>

    <!-- Add a new comment -->
    <?php global $user;
    if ($user->is_logged_in()) : ?>
        <form class="m-3 mb-0 p-3 pb-0 border rounded" action="<?php echo $_SERVER['PHP_SELF'] . "?tconst=" . $title['tconst']; ?>" method="post">
            <h3>Post a Comment</h3>
            <div class="form-group">
                <b>Posting as: <?php echo $user->get_email(); ?></b>
            </div>
            <div class="form-group">
                <!-- <label for="commentTextArea">Comment</label> -->
                <textarea class="form-control" id="commentTextArea" name="commentTextArea" rows="3"></textarea>
            </div>
            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">Post comment</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include("include/boilerplate/tail.php"); ?>