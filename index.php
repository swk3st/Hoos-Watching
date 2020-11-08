<?php
require_once("include/db_interface.php");
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Hoo's Watching</title>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <div class="container">
        <h1 class="mt-5">Hoo's Watching</h1>
        <ol>
            <?php
            $top_five = get_top_five_movies();
            foreach ($top_five as $title) :
            ?>
                <li>
                    <?php echo $title; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="container">
        <?php
        // $user_exists = check_user_exists("pwt5ca@virginia.edu");
        // echo json_encode($user_exists);

        if (create_new_user('pwt5ca@virginia.edu', 'my_password')) {
            echo "Created a new user!";
        } else {
            echo "Failed to create a new user: user already exists.";
        }

        // $user_exists = check_user_exists("pwt5ca@virginia.edu");
        // echo json_encode($user_exists);
        ?>
    </div>
</body>

</html>