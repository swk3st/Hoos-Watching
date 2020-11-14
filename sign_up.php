<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");
require_once("include/images.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle user creation form.
    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['conf_pass'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['conf_pass'];
        if ($password != $confirm_password) {
            global $ERROR;
            $ERROR = "Account creation failed: passwords do not match.";
        } else if (check_user_exists($email)) {
            global $ERROR;
            $ERROR = "Account creation failed: user already exists.";
        } else {
            create_new_user($email, $password);
            if (check_user_exists($email)) {
                // global $MESSAGE;
                // $MESSAGE = "User creation succeeded! Now log in below.";
            } else {
                global $ERROR;
                $ERROR = "Account creation failed; database failure or password doesn't meet requirements.";
            }
        }
    }
}

global $user;
// Redirect back to the home page if the user is actually logged in.
if ($user->is_logged_in()) {
    header("Location: ./index.php");
    die();
}

$HEADER_INFO = array(
    "Hoo's Watching | Register",
    "Hoo's Watching",
    "Register for an account"
);
include("include/boilerplate/head.php");
?>

<link rel="stylesheet" href="assets/css/signup.css">
<div id="login">
    <!-- <h3 class="text-center text-white pt-5">Register</h3> -->
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center my-5">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="" method="post">
                        <h3 class="text-center text-info">Register</h3>
                        <div class="form-group">
                            <label for="username" class="text-info">Email:</label><br>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="text-info">Password:</label><br>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="text-info">Confirm Password:</label><br>
                            <input type="password" name="conf_pass" id="conf_pass" class="form-control">
                            <p class="text-muted mt-1">Password must contain upper and lowercase letters, at least one number, at least one symbol, and be 12 or more characters long.</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-info btn-md">Sign up</button>
                            <a href="sign_in.php" class="btn btn-link btn-md" style="color: #17a2b8!important;">Log in here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/boilerplate/tail.php"); ?>