<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");
require_once("include/images.php");


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
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="" method="post">
                        <h3 class="text-center text-info">Login</h3>
                        <div class="form-group">
                            <label for="username" class="text-info">Email:</label><br>
                            <input type="text" name="email" id="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password" class="text-info">Password:</label><br>
                            <input type="text" name="password" id="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="remember-me" class="text-info"><span>Remember me</span> <span><input id="remember-me" name="remember-me" type="checkbox"></span></label><br>
                            <input type="submit" name="submit" class="btn btn-info btn-md" value="submit">
                        </div>
                        <div id="register-link" class="text-right">
                            <a href="sign_up.html" class="text-info">Register here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>