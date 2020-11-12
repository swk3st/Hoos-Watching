<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");

$title = null;
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['tconst'])) {
        $title = title_get_info($_GET['tconst']);
    }
}

// Redirect back to the home page if the title isn't valid.
if (is_null($title)) {
    header("Location: ./index.php");
    die();
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
    <h3 class='mt-5'>Comments</h3>

    <?php
    $comments = title_get_comments($title['tconst']);
    foreach ($comments as $comment) :
    ?>
        <div class="media border border-rounded mt-3 p-2">
            <img src="assets/img/noun_person_124296.png" class="mr-3" alt="Profile picture" width=64 height=64>
            <div class="media-body">
                <h5 class="mt-0"><?php echo $comment['email']; ?></h5>
                <?php echo $comment['text']; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (sizeof($comments) > 0) : ?>
        <small class="text-muted">person by Diego Naive from the Noun Project</small>
    <?php endif; ?>
</div>

<?php include("include/boilerplate/tail.php"); ?>