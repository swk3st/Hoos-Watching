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
            <?php $poster = get_poster($title['tconst']); ?>
            <img class="w-100" src="<?php echo $poster; ?>" alt="<?php echo $title['primaryTitle']; ?> poster">
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
                            <th scope="row">Average Rating (HW)</th>
                            <td><?php echo number_format($title['userRating'], 1); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Number of Votes (HW)</th>
                            <td><?php echo number_format($title['numUserVotes']); ?></td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <th scope="row">Average Rating (HW)</th>
                            <td>Be the first to vote!</td>
                        </tr>
                        <tr>
                            <th scope="row">Number of Votes (HW)</th>
                            <td>0</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php foreach ($title as $key => $value) : ?>
                <p>
                    <b><?php echo $key; ?></b> - <?php echo $value; ?>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container">
    <h3 class="mt-5">People</h3>

    <?php
    $people = title_get_people($title['tconst']);
    foreach ($people as $person) :
    ?>
        <p><?php echo json_encode($person); ?></p>
    <?php endforeach; ?>
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
                        <h5 class="mt-0"><?php echo $comment['email']; ?><small class="text-muted"> at <?php echo $comment['date_added']; ?></small></h5>
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
        <form class="m-3 p-3 pb-0 border rounded" action="<?php echo $_SERVER['PHP_SELF'] . "?tconst=" . $title['tconst']; ?>" method="post">
            <div class="form-group">
                <b>Posting as: <?php echo $user->get_email(); ?></b>
            </div>
            <div class="form-group">
                <label for="commentTextArea">Comment</label>
                <textarea class="form-control" id="commentTextArea" name="commentTextArea" rows="3"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Post comment</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include("include/boilerplate/tail.php"); ?>