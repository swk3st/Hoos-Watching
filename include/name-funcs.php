<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("db_interface.php");

function name_get_info($nconst)
{
    $sql = "SELECT primaryName, birthYear, deathYear FROM Names
    WHERE nconst=?";

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
    $sql = "SELECT tconst, category, job, characters, ( SELECT CONVERT(JSON_ARRAYAGG(primaryProfession) USING utf8) FROM Professions as p WHERE p.nconst = n.nconst GROUP BY n.nconst )
    FROM Names as n NATURAL JOIN PeopleToTitleData
    WHERE nconst=?
    ORDER BY ordering ASC";

    global $db;

    $statement = $db->prepare($sql);
    $statement->bind_param("s", $nconst);
    $statement->execute();

    $tconst = null;
    $category = null;
    $job = null;
    $characters = null;
    $professions = null;
    $statement->bind_result($tconst, $category, $job, $characters, $professions);

    $output = array();
    while ($statement->fetch()) {
        array_push($output, array(
            "tconst" => $tconst,
            "category" => $category,
            "job" => $job,
            "characters" => json_decode($characters, true),
            "professions" => json_decode($professions, true)
        ));
    }

    $statement->close();

    return $output;
}
