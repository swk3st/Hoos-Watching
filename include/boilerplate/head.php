<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-- Sticky footer -->
    <link href="assets/css/footer.css" rel="stylesheet" crossorigin="anonymous">

    <title>
        <?php
        global $HEADER_INFO;
        echo $HEADER_INFO[0];
        ?>
    </title>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <?php
    include("navbar.php");
    ?>
    <div class="jumbotron jumbotron-fluid bg-primary text-light">
        <div class="container">
            <h1 class="display-4">
                <?php
                global $HEADER_INFO;
                echo $HEADER_INFO[1];
                ?>
            </h1>
            <p class="lead">
                <?php
                if (isset($HEADER_INFO[2])) {
                    global $HEADER_INFO;
                    echo $HEADER_INFO[2];
                }
                ?>
            </p>
        </div>
    </div>

    <?php
    global $ERROR;
    if (isset($ERROR)) :
    ?>
        <div class="container">
            <div class="alert alert-danger" role="alert">
                <?php echo $ERROR; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php
    global $MESSAGE;
    if (isset($MESSAGE)) :
    ?>
        <div class="container">
            <div class="alert alert-primary" role="alert">
                <?php echo $MESSAGE; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    <?php endif; ?>