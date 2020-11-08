<?php

define("HASH_ALGO", "sha256");

/**
 * Hash a user's password with the configured hash algorithm. This does
 * not use password_hash since the CS server only uses PHP 5.4, which
 * predates password_* functions.
 */
function movie_password_hash($password)
{
    return hash(HASH_ALGO, $password);
}

/**
 * Verify a password against a hash.
 * 
 * This function is vulnerable to timing attacks.
 */
function movie_check_password($password, $password_hash)
{
    $attempted_hash = hash(HASH_ALGO, $password);
    return (bool) $attempted_hash === $password_hash;
}

function test_security()
{
    $my_password = "apples123";
    $my_password_hashed = movie_password_hash($my_password);
    echo "My password: " . $my_password . "<br>";
    echo "Hash of my password: " . $my_password_hashed  . "<br>";
    echo "My password === apples123: " . movie_check_password($my_password, $my_password_hashed) . "<br>";
    echo "My password === Apples123: " . movie_check_password("Apples123", $my_password_hashed) . "<br>";
}
