<?php

define("IMAGE_DIRECTORY", "assets/img/downloaded/");

/**
 * Try to get the url for an image. If it doesn't exist, then download and cache it.
 */
function get_poster($tconst) {
    $fname = $tconst . ".jpg";
    $full_path = IMAGE_DIRECTORY . $fname;
    if (!file_exists($full_path)) {
        $poster_url = title_get_poster($tconst);
        file_put_contents($full_path, file_get_contents($poster_url));
    }
    return $full_path;
}
