<?php

require_once("include/db_interface.php");
require_once("include/title.php");
require_once("include/user.php");
require_once("include/util.php");

$title = null;
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['tconst'])) {
        $title = title_get_info($_GET['tconst']);
    }
}

if (is_null($title)) {
    // Redirect back to the home page.
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

<?php include("include/boilerplate/tail.php"); ?>