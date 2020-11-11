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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['commentTextArea'])) {
        global $user;
        if (!$user->create_comment_on_title($title['tconst'], $_POST['commentTextArea'])) {
            debug_echo("Create comment failed.");
        } else {
            global $MESSAGE;
            $MESSAGE = "Comment posted successfully!";
        }
    }
}

$HEADER_INFO = array(
    "Hoo's Watching | " . $title['primaryTitle'],
    $title['primaryTitle'] . " <small class='text-muted'> <a href=\"./index.php\">Hoo's Watching</a></small> ",
    "Hoo's Watching | " . $title['primaryTitle']
);




include("include/boilerplate/head.php");
?>

<div class="container">
    <?php
    foreach ($title as $key => $value) :
    ?>
        <p>
            <b><?php echo $key; ?></b> - <?php echo $value; ?>
        </p>
    <?php
    endforeach;
    ?>
</div>

<div class="container">
    <?php $poster = get_poster($title['tconst']); ?>
    <img class='w-25' src="<?php echo $poster; ?>" alt="">
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
                <li class="media border border-rounded mt-3 p-2">
                    <img src="assets/img/noun_person_124296.png" class="mr-3" alt="Profile picture" width=64 height=64>
                    <div class="media-body">
                        <h5 class="mt-0"><?php echo $comment['email']; ?></h5>
                        <p><?php echo $comment['text']; ?></p>

                        <?php if ($comment['email'] == $user->get_email()) : ?>
                        <form action="" method="post">
                        <button type="button" class="btn btn-danger btn-sm">Delete this comment</button>
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