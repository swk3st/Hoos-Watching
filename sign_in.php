<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");
require_once("include/images.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle login form.
    if (isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {
        if (!isset($_POST['remember-me']) || !$_POST['remember-me']) {
            /* set the cache limiter to 'private' */
            session_cache_limiter('private');
            $cache_limiter = session_cache_limiter();

            /* set the cache expire to 30 minutes */
            session_cache_expire(30);
            $cache_expire = session_cache_expire();
        }
        $login_succeeded = login_user($_POST['loginEmail'], $_POST['loginPassword']);
        if ($login_succeeded) {
            // Reinit the user so they are logged in.
            global $user;
            $user = new User();

            global $MESSAGE;
            $MESSAGE = "Login succeeded!";
        } else {
            global $MESSAGE;
            $MESSAGE = "Login failed! Incorrect username or password.";
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
    "Hoo's Watching | Log in",
    "Hoo's Watching",
    "Log in"
);
include("include/boilerplate/head.php");
?>

<link rel="stylesheet" href="assets/css/signin.css">
<div id="login">
    <!-- <h3 class="text-center text-white pt-5">Hoo's Watching Login</h3> -->
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center my-5">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="" method="post">
                        <h3 class="text-center text-info">Login</h3>
                        <div class="form-group">
                            <label for="username" class="text-info">Email:</label><br>
                            <input type="text" name="loginEmail" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="text-info">Password:</label><br>
                            <input type="password" name="loginPassword" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="remember-me" class="text-info">
                                <span> <input id="remember-me" name="remember-me" type="checkbox"> </span>
                                <span>Remember me</span>
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-info btn-md">Log In</button>
                            <a href="sign_up.html" class="btn btn-link btn-md" style="color: #17a2b8!important;">Register here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("include/boilerplate/tail.php"); ?>