<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("db_interface.php");

function name_get_info($nconst)
{
    $sql = "SELECT primaryName, birthYear, deathYear FROM Names WHERE nconst=?";

    global $db;

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $nconst);
    $statement->execute();

    $primaryName = null;
    $birthYear = null;
    $deathYear = null;
    $statement->bind_result($primaryName, $birthYear, $deathYear);
    $statement->fetch();

    $output = array(
        "nconst" => $nconst,
        "primaryName" => $primaryName,
        "birthYear" => $birthYear,
        "deathYear" => $deathYear,
    );

    $statement->close();

    return $output;
}

function name_get_roles($nconst)
{
    $sql = "SELECT tconst, category, job, characters, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes FROM PeopleToTitleData NATURAL JOIN Titles WHERE nconst=? ORDER BY startYear ASC";

    global $db;

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $nconst);
    $statement->execute();
    
    $tconst = null;
    $category = null;
    $job = null;
    $characters = null;
    $titleType = null;
    $primaryTitle = null;
    $originalTitle = null;
    $isAdult = null;
    $startYear = null;
    $endYear = null;
    $runtimeMinutes = null;
    $averageRating = null;
    $numVotes = null;
    $statement->bind_result($tconst, $category, $job, $characters, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes);

    $output = array();
    while ($statement->fetch()) {
        array_push($output, array(
            "tconst" => $tconst,
            "category" => $category,
            "job" => $job,
            "characters" => json_decode($characters, true),
            "titleType" => $titleType,
            "primaryTitle" => $primaryTitle,
            "originalTitle" => $originalTitle,
            "isAdult" => $isAdult,
            "startYear" => $startYear,
            "endYear" => $endYear,
            "runtimeMinutes" => $runtimeMinutes,
            "averageRating" => $averageRating,
            "numVotes" => $numVotes,
        ));
    }

    $statement->close();

    return $output;
}
