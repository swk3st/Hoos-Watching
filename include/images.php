<?php

define("IMAGE_DIRECTORY", "assets/img/downloaded/");
define("IMAGE_NO_POSTER", "assets/img/downloaded/no-poster.jpg");

/**
 * Try to get the url for an image. If it doesn't exist, then download and cache it.
 */
function get_poster($tconst)
{
    if (is_null($tconst)) {
        return IMAGE_NO_POSTER;
    }

    $fname = $tconst . ".jpg";
    $full_path = IMAGE_DIRECTORY . $fname;
    if (!file_exists($full_path)) {
        $poster_url = title_get_poster($tconst);
        if (is_null($poster_url)) {
            // Try to copy generic poster in.
            file_put_contents($full_path, file_get_contents(IMAGE_NO_POSTER));
        } else {
            file_put_contents($full_path, file_get_contents($poster_url));
        }
    }
    return $full_path;
}
