<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

define("SORT_TITLES_PRIMARY_TITLE", "primaryTitle");
define("SORT_TITLES_AVERAGE_RATING", "averageRating");
define("SORT_TITLES_NUM_VOTES", "numVotes");
define("SORT_TITLES_NUM_STARS", "averageRating*numVotes");
define("SORT_TITLES_YEAR", "startYear");
define("SORT_TITLES_LENGTH", "runtimeMinutes");
define("SORT_TITLE_USER_RATING", "userRating {order}, averageRating");
define("SORT_TITLE_NUM_USER_RATINGS", " numUserRatings {order}, numVotes");
$ALL_TITLE_SORTS = array(
    SORT_TITLES_PRIMARY_TITLE,
    SORT_TITLES_AVERAGE_RATING,
    SORT_TITLES_NUM_VOTES,
    SORT_TITLES_NUM_STARS,
    SORT_TITLES_YEAR,
    SORT_TITLES_LENGTH,
    SORT_TITLE_USER_RATING,
    SORT_TITLE_NUM_USER_RATINGS
);

define("FILTER_TITLES_NONE", "");
define("FILTER_TITLES_PRIMARY_TITLE", "WHERE primaryTitle LIKE \"%?%\"");
define("FILTER_TITLES_AVG_RATING", "WHERE averageRating > ?");
define("FILTER_TITLES_USER_RATING", " WHERE averageRating > ?");
define("FILTER_TITLES_GENRE", "WHERE Genres.genres=\"?\"");
define("FILTER_TITLES_TYPE", "WHERE titleType=\"?\"");
$ALL_TITLE_FILTERS = array(
    FILTER_TITLES_NONE,
    FILTER_TITLES_PRIMARY_TITLE,
    FILTER_TITLES_AVG_RATING,
    FILTER_TITLES_USER_RATING,
    FILTER_TITLES_GENRE,
    FILTER_TITLES_TYPE,
);

/**
 * Get titles in a certain range, sorted via one of many defined ways:
 * 
 * SORT_TITLES_PRIMARY_TITLE - via primary title
 * SORT_TITLES_AVERAGE_RATING - via average IMDb vote
 * SORT_TITLES_NUM_VOTES - via number of IMDb votes
 * SORT_TITLES_NUM_STARS - via number of IMDb votes times the average number of stars
 * SORT_TITLES_YEAR - via the title's year
 * SORT_TITLE_USER_RATING - via the site's rating for a movie
 * SORT_TITLE_NUM_USER_RATINGS - via the number of user ratings for a movie
 */
function get_titles($start, $end, $sort_type = SORT_TITLES_NUM_STARS, $filter_type = FILTER_TITLES_NONE, $filter_value = null, $ascending = true)
{
    global $db;
    global $ALL_TITLE_SORTS;
    global $ALL_TITLE_FILTERS;

    // Input validation.
    if ($end <= $start && $end > 0 && $start > 0) {
        die("get_titles: End of query range must be beyond the start point.");
    }
    if (!in_array($sort_type, $ALL_TITLE_SORTS)) {
        die("get_titles: Invalid sort type \"$sort_type\".");
    }
    if (!in_array($filter_type, $ALL_TITLE_FILTERS)) {
        die("get_titles: Invalid filter type \"$filter_type\".");
    }

    // Do some cleaning and replacing for the filter.
    $filter_value = mysqli_escape_string($db, $filter_value);
    $built_filter = str_replace("?", $filter_value, $filter_type);

    // Now build the sql command.
    $sql = "SELECT DISTINCT tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating,numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as numUserRatings
    FROM Titles AS t
{filter}
ORDER BY {sort} {order}
LIMIT {start}, {count}";
    if ($filter_type == FILTER_TITLES_GENRE) {
        $sql = "SELECT DISTINCT tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating,numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as numUserRatings
FROM Titles AS t
NATURAL JOIN Genres {filter}
ORDER BY {sort} {order}
LIMIT {start}, {count}";
    } else if ($filter_type == FILTER_TITLES_USER_RATING || $sort_type == SORT_TITLE_USER_RATING || $sort_type == SORT_TITLE_NUM_USER_RATINGS) {
        $sql = "SELECT DISTINCT tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating,numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as numUserRatings
FROM Titles AS t
{filter}
ORDER BY {sort} {order}
LIMIT {start}, {count}";
    }

    // Ascending or descending.
    $order = "DESC";
    if ($ascending) {
        $order = "ASC";
    }

    // Replace in values.
    $sql = str_replace("{filter}", $built_filter, $sql);
    $sql = str_replace("{sort}", $sort_type, $sql);
    $sql = str_replace("{order}", $order, $sql);
    $sql = str_replace("{start}", strval((int) $start), $sql);
    $sql = str_replace("{count}", strval((int) $end - (int) $start), $sql);

    debug_echo($sql);

    // Now perform the query.
    $statement = $db->prepare($sql);
    $statement->execute();

    $tconst = null;
    $titleType = null;
    $primaryTitle = null;
    $originalTitle = null;
    $isAdult = null;
    $startYear = null;
    $endYear = null;
    $runtimeMinutes = null;
    $averageRating = null;
    $numVotes = null;
    $userRating = null;
    $numUserVotes = null;

    $statement->bind_result($tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes);
    $statement->fetch();

    $output = array();
    while ($statement->fetch()) {
        array_push($output, array(
            "tconst" => $tconst,
            "titleType" => $titleType,
            "primaryTitle" => $primaryTitle,
            "originalTitle" => $originalTitle,
            "isAdult" => $isAdult,
            "startYear" => $startYear,
            "endYear" => $endYear,
            "runtimeMinutes" => $runtimeMinutes,
            "averageRating" => $averageRating,
            "numVotes" => $numVotes,
            "userRating" => $userRating,
            "numUserVotes" => $numUserVotes,
        ));
    }

    $statement->close();

    return $output;
}

function title_get_info($tconst)
{
    global $db;

    $sql = "SELECT tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as numUserRatings
FROM Titles as t
WHERE tconst=?";

    // Now perform the query.
    $statement = $db->prepare($sql);
    $statement->bind_param("s", $tconst);
    $statement->execute();

    $tconst = null;
    $titleType = null;
    $primaryTitle = null;
    $originalTitle = null;
    $isAdult = null;
    $startYear = null;
    $endYear = null;
    $runtimeMinutes = null;
    $averageRating = null;
    $numVotes = null;
    $userRating = null;
    $numUserVotes = null;
    $statement->bind_result($tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes);
    $statement->fetch();

    $output = array(
        "tconst" => $tconst,
        "titleType" => $titleType,
        "primaryTitle" => $primaryTitle,
        "originalTitle" => $originalTitle,
        "isAdult" => $isAdult,
        "startYear" => $startYear,
        "endYear" => $endYear,
        "runtimeMinutes" => $runtimeMinutes,
        "averageRating" => $averageRating,
        "numVotes" => $numVotes,
        "userRating" => $userRating,
        "numUserVotes" => $numUserVotes,
    );

    $statement->close();
    return $output;
}

function title_get_comments($tconst)
{
    $sql = "SELECT email, date_added, text, likes FROM Comment WHERE tconst=? ORDER BY date_added ASC";

    global $db;

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $tconst);
    $statement->execute();

    $email = null;
    $date_added = null;
    $text = null;
    $likes = null;
    $statement->bind_result($email, $date_added, $text, $likes);

    $output = array();
    while ($statement->fetch()) {
        array_push($output, array(
            "email" => $email,
            "date_added" => $date_added,
            "text" => $text,
            "likes" => $likes
        ));
    }

    $statement->close();

    return $output;
}
