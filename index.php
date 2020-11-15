<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");

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

$HEADER_INFO = array(
    "Hoo's Watching",
    "Hoo's Watching"
);
include("include/boilerplate/head.php");

include("include/searchBar.php");

?>

<div class="container">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <!-- tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes -->
                <th scope="col">Title</th>
                <th scope="col">Year</th>
                <th scope="col">Length</th>
                <th scope="col">IMDb rating</th>
                <th scope="col">HW rating</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $titles = get_titles(0, 25, SORT_TITLES_NUM_STARS, FILTER_TITLES_NONE, null, false);
            foreach ($titles as $title) :
            ?>
                <tr>
                    <th scope="row">
                        <a href="./title.php?tconst=<?php echo $title['tconst']; ?>">
                            <?php echo $title['primaryTitle']; ?>
                        </a>
                        <small class="text-muted"><?php echo $title['titleType']; ?></small>
                    </th>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="container">
    <h2>User</h2>
    <p>Current user:
        <?php
        global $user;
        if (!$user->is_logged_in()) : ?>
            Not logged in!
        <?php else : ?>
            <p>
                <a href="<?php echo "./user.php?email=" . $user->get_email() ?>
                "><?php echo $user->get_email(); ?>
                </a>
            </p>
        <?php endif ?>

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

<?php include("include/boilerplate/tail.php"); ?>