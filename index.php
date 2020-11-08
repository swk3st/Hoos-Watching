<?php

require_once("include/db_interface.php");
require_once("include/user.php");
require_once("include/title.php");

// Handle POST requests.
$login_succeeded = null;
$creation_succeeded = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {
        $login_succeeded = login_user($_POST['loginEmail'], $_POST['loginPassword']);
        if ($login_succeeded) {
            // Reinit the user so they are logged in.
            global $user;
            $user = new User();
        }
    }

    if (isset($_POST['createEmail']) && isset($_POST['createPassword'])) {
        if (check_user_exists($_POST['createEmail'])) {
            $creation_succeeded = false;
        } else {
            create_new_user($_POST['createEmail'], $_POST['createPassword']);
            $creation_succeeded = check_user_exists($_POST['createEmail']);
        }
    }
}
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
        <h1 class="mt-5"><a href="<?php echo $_SERVER['PHP_SELF']; ?>">Hoo's Watching</a></h1>

        <ol>
            <?php
            $titles = get_titles(0, 25, SORT_TITLE_USER_RATING, FILTER_TITLES_USER_RATING, 2, false);
            foreach ($titles as $title) :
            ?>
                <li>
                    <?php echo $title['primaryTitle']; ?>
                    <?php echo $title['startYear']; ?>
                    <?php echo json_encode($title); ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="container">
        <h2>User</h2>
        <p>Current user:
            <?php
            global $user;
            if (!$user->is_logged_in()) {
                echo "Not logged in!";
            } else {
                echo $user->get_email();
            }
            ?>
        </p>

        <div class="border rounded mt-3 p-3">
            <h3>Log in to an account</h3>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- From https://getbootstrap.com/docs/4.0/components/forms/ -->
                <div class="form-group">
                    <label for="loginEmail">Email address</label>
                    <input type="email" class="form-control" id="loginEmail" name="loginEmail" aria-describedby="emailHelp" placeholder="Enter email">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>

        <div class="border rounded mt-3 p-3">
            <h3>Create an account</h3>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <!-- From https://getbootstrap.com/docs/4.0/components/forms/ -->
                <div class="form-group">
                    <label for="createEmail">Email address</label>
                    <input type="email" class="form-control" id="createEmail" name="createEmail" aria-describedby="emailHelp" placeholder="Enter email">
                    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                </div>
                <div class="form-group">
                    <label for="createPassword">Password</label>
                    <input type="password" class="form-control" id="createPassword" name="createPassword" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>