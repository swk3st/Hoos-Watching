<?php

require_once("connect.php");

session_start();

/**
 * Create a new user given an email and a password.
 */
function create_new_user($email, $password) {
    global $db;

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Users (email, password) VALUES (:email, :password)";

    $statement = $db->prepare($sql);
    $statement->execute(array(
        ":email" => $email,
        ":password" => $password_hashed,
    ));
    $statement->closeCursor();
}

/**
 * Log into a user and set a session variable indicating what user is logged in currently.
 */
function login_user($email, $password) {
    global $db;

    $sql = "SELECT password FROM Users WHERE email=:email";

    $statement = $db->prepare($sql);
    $statement->execute(array(
        ":email" => $email,
    ));
    $results = $statement->fetch();
    if ($results) {
        if (password_verify($password, $results['password'])) {
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password; // This is a bad practice.
            $statement->closeCursor();
            return true;
        }
    }
    $statement->closeCursor();
    return false;
}

/**
 * Return true if the current user is logged in, otherwise return false.
 */
function is_logged_in() {
    if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
        return false;
    }
    $email = $_SESSION['email'];
    $password = $_SESSION['password']; // This is a bad practice.

    return login_user($email, $password);
}
