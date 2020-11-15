<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

define("DEBUG", true);

if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function debug_echo($value)
{
    // if (!DEBUG) {
    //     return;
    // }
    // $s = strval($value);
    // echo "<div class=\"container border rounded border-danger mt-3 mb-3 p-3\"> $s </div>";
}
