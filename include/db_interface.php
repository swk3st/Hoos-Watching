<?php

require_once("connect.php");

session_start();

/**
 * Check if a username is already used.
 */
function check_user_exists($email)
{
    global $db;

    $sql = "SELECT count(*) FROM Users WHERE email=?";

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $email);
    $results = $statement->execute();
    $user_exists = 0;
    $statement->bind_result($user_exists);
    if ($results) {
        $statement->fetch();
        return $user_exists == 1;
    }
    $statement->close();
    return false;
}

/**
 * Create a new user given an email and a password.
 */
function create_new_user($email, $password)
{
    // global $db;

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    // $sql = "INSERT INTO Users (email, password) VALUES (?, ?)";

    // $statement = $db->prepare($sql);
    echo "yay $email $password_hashed";
    // $statement->bind_param("ss", $name, $password_hashed);
    // $statement->execute();
    // // if (!$statement->execute()) {
    // //     die("Error executing create_new_user query.");
    // // }
    // $statement->close();
}

/**
 * Log into a user and set a session variable indicating what user is logged in currently.
 */
function login_user($email, $password)
{
    global $db;

    $sql = "SELECT password FROM Users WHERE email=?";

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $email);
    $results = $statement->fetch();
    $stored_password = "";
    $statement->bind_result($stored_password);
    if ($results) {
        if (password_verify($password, $stored_password)) {
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password; // This is a bad practice.
            $statement->close();
            return true;
        }
    }
    $statement->close();
    return false;
}

/**
 * Return true if the current user is logged in, otherwise return false.
 */
function is_logged_in()
{
    if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
        return false;
    }
    $email = $_SESSION['email'];
    $password = $_SESSION['password']; // This is a bad practice.

    return login_user($email, $password);
}

/**
 * Return true if the current user is logged in, otherwise return false.
 * 
 * From https://www.php.net/manual/en/mysqli-stmt.fetch.php
 */
function get_top_five_movies()
{
    global $db;
    $sql = "SELECT primaryTitle FROM pwt5ca.Titles ORDER BY startYear DESC LIMIT 5";
    $output = array();

    if ($stmt = $db->prepare($sql)) {

        /* execute statement */
        $stmt->execute();

        /* bind result variables */
        $title = "";
        $output = array();
        $stmt->bind_result($title);

        /* fetch values */
        while ($stmt->fetch()) {
            array_push($output, $title);
        }

        /* close statement */
        $stmt->close();
    }

    return $output;
}
