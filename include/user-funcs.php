<?php

/**
 * CS4750
 * Hoo's Watching
 * Jessica Heavner (jlh9qv), Julian Cornejo Castro (jac9vn), Patrick Thomas (pwt5ca), & Solimar Kwa (swk3st)
 */

require_once("db_interface.php");

class User
{
    /** The currently logged in user's email. */
    private $email = null;

    /** The currently logged in user's password. Should be fairly secure since it 
     * is stored server-side, but it is still bad practice to store a password in 
     * memory for longer than it needs to be. */
    private $password = null;

    private $is_self = null;

    /**
     * Initialize the current user.
     */
    public function __construct($email = null)
    {
        if (is_null($email)) {
            // First try to log in to the user with session variables.

            // Load the user session if need be.
            if (!isset($_SESSION)) {
                session_start();
            }

            // Make sure that there is a logged in user.
            if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
                return;
            }
            $email = $_SESSION['email'];
            $password = $_SESSION['password']; // This is a bad practice.

            if (login_user($email, $password)) {
                $this->email = $email;
                $this->password = $password;
                $this->is_self = true;
            }

            unset($email);
            unset($password);
        } else {
            // Otherwise initialize based on the provided email, given that it is valid.
            if (check_user_exists($email)) {
                $this->email = $email;
                $this->is_self = false;
            } else {
                debug_echo("User does not exist!");
            }
        }
    }

    /**
     * Return whether or not the current user is logged in.
     * 
     * @return bool True if there is a logged in user, false otherwise.
     */
    public function is_logged_in()
    {
        return !(is_null($this->email) || is_null($this->password)) && $this->is_self;
    }

    /**
     * Return whether or not the current user is initialized (provides access to getters).
     * 
     * @return bool True if initialized, false otherwise.
     */
    private function is_initialized()
    {
        return !is_null($this->is_self);
    }

    /**
     * Get the email for the current user.
     * 
     * @return str Email of current user.
     */
    public function get_email()
    {
        if (!is_null($this->email)) {
            return $this->email;
        }
        return null;
    }

    /**
     * Get the account creation date for the current user.
     * 
     * @return str Date the account was created.
     */
    public function get_creation_date()
    {
        global $db;

        $sql = "SELECT sign_up_date FROM Users WHERE email=?";

        $email = $this->get_email();
        $statement = $db->prepare($sql);
        $statement->bind_param("s", $email);
        $statement->execute();

        $date = null;
        $statement->bind_result($date);
        $statement->fetch();
        $statement->close();
        return $date;
    }

    /**
     * Add a friend to the current user's friends.
     * 
     * @param str $email The email of the friend.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function add_friend($email)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO Friends (email, friends_email)" .
            "VALUES (?, ?)";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $email);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Remove a friend from the current user's friends.
     * 
     * @param str $email The email of the friend.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function remove_friend($email)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "DELETE FROM Friends WHERE email=? AND friends_email=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $email);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Get an array of all of the user's friends.
     * 
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function get_friends()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT friends_email FROM Friends WHERE email=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $friend_email = null;
        $statement->bind_result($friend_email);

        $output = array();
        while ($statement->fetch()) {
            array_push($output, $friend_email);
        }

        $statement->close();

        return $output;
    }

    public function get_friends_count()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return null;
        }

        $sql = "SELECT count(friends_email) FROM Friends WHERE email=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return null;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    public function name_add_favorite($nconst, $order = null)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        if (is_null($order)) {
            $order = $this->get_favorite_person_count() + 1;
        }

        $sql = "INSERT INTO UserToPersonData (email, nconst, personOrder) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE personOrder=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $nconst, $order, $order);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function name_remove_favorite($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET personOrder=NULL WHERE email=? AND nconst=? ";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function name_add_rating($nconst, $number_of_stars)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO UserToPersonData (email, nconst, number_of_stars) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE number_of_stars=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $nconst, $number_of_stars, $number_of_stars);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function name_remove_rating($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET number_of_stars=NULL WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function get_favorite_people()
    {
        $sql = "SELECT nconst, primaryName, birthYear, deathYear, ( SELECT CONVERT(JSON_ARRAYAGG(primaryProfession) USING utf8) FROM Professions as p WHERE p.nconst = n.nconst GROUP BY n.nconst ) FROM Names as n NATURAL JOIN UserToPersonData WHERE email=?";

        global $db;

        $statement = $db->prepare($sql);
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $nconst = null;
        $primaryName = null;
        $birthYear = null;
        $deathYear = null;
        $professions = null;
        $statement->bind_result($nconst, $primaryName, $birthYear, $deathYear, $professions);

        $output = array();
        while ($statement->fetch()) {
            array_push($output, array(
                "nconst" => $nconst,
                "primaryName" => $primaryName,
                "birthYear" => $birthYear,
                "deathYear" => $deathYear,
                "professions" => json_decode($professions, true)
            ));
        }

        $statement->close();

        return $output;
    }

    public function get_favorite_person_count()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return null;
        }

        $sql = "SELECT count(*) FROM UserToPersonData WHERE email=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return null;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    public function is_favorite_person($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "SELECT count(*) FROM UserToPersonData WHERE email=? AND nconst=? AND personOrder IS NOT NULL";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return ($count > 0);
    }

    public function get_favorite_person_stars($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "SELECT number_of_stars FROM UserToPersonData WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    /**
     * Add a friend to the current user's friends.
     * 
     * @param str $email The email of the friend.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function is_friend($email)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "SELECT count(*) FROM Friends WHERE email=? AND friend_email=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $email);
        $statement->execute();
        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return ($count > 0);
    }

    /** 
     * Create a public comment on a specific title.
     * 
     * @param str $tconst The title identifier.
     * @param str $text The body of the comment.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function create_comment_on_title($tconst, $text)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO Comment(tconst, email, date_added, text, likes) VALUES (?, ?, CURRENT_TIMESTAMP, ?, 0)";
        $text_cleaned =  htmlspecialchars($text);

        global $db;

        $email = $this->get_email();
        $statement = $db->prepare($sql);
        $statement->bind_param("sss", $tconst, $email, $text_cleaned);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** 
     * Create a public comment on a specific title.
     * 
     * @param str $tconst The title identifier.
     * @param str $text The body of the comment.
     * @param str $date_added The time that the comment was originally posted.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function like_comment_on_title($tconst, $email, $date_added)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE `Comment` SET likes=likes+1 WHERE tconst=? AND email=? AND date_added=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssss", $tconst, $email, $likes, $date_added);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function remove_comment_on_title($tconst, $date_added)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "DELETE FROM Comment WHERE email=? AND tconst=? AND date_added=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("sss", $this->email, $tconst, $date_added);
        $statement->execute();
        $statement->close();

        return true;
    }


    public function movie_get_next_watch_list_order()
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return null;
        }

        $sql = "SELECT max(watchOrder) FROM UserToTitleData WHERE email=?;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return null;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $max_order = null;
        $statement->bind_result($max_order);
        $statement->fetch();
        $statement->close();

        return $max_order;
    }

    public function movie_is_on_watch_list($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return null;
        }

        $sql = "SELECT count(*) FROM UserToTitleData WHERE email=? AND tconst=? AND watchOrder IS NOT NULL;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return ($count > 0);
    }

    public function movie_get_next_favorites_rank()
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return null;
        }

        $sql = "SELECT max(favoritesRank) FROM UserToTitleData WHERE email=?;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return null;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $max_order = null;
        $statement->bind_result($max_order);
        $statement->fetch();
        $statement->close();

        return $max_order;
    }

    public function movie_is_favorite($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return null;
        }

        $sql = "SELECT count(*) FROM UserToTitleData WHERE email=? AND tconst=? AND favoritesRank IS NOT NULL;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return ($count > 0);
    }

    /**
     * Add a movie to the current user's watch list.
     * 
     * @param str $tconst The title identifier.
     * @param int $watch_order The order to put the movie on the list.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function movie_add_to_watch_list($tconst, $watch_order = null)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        if (is_null($watch_order)) {
            $watch_order = $this->movie_get_next_favorites_rank() + 1;
        }

        $sql = "INSERT INTO UserToTitleData (email, tconst, watchOrder, date_added)
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ON DUPLICATE KEY UPDATE watchOrder=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $tconst, $watch_order, $watch_order);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Change the order of a title on the current user's watch list.
     * 
     * @param str $tconst The title identifier.
     * @param int $watch_order The order to put the movie on the list.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function movie_change_watch_list_order($tconst, $watch_order)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET watchOrder=? WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("iss", $watch_order, $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Remove a movie from the current user's watch list.
     * 
     * @param str $tconst The title identifier.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function movie_remove_from_watch_list($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET watchOrder=NULL WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Add a title to the user's favorites. */
    public function movie_add_to_favorites($tconst, $favoritesRank = null)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        if (is_null($favoritesRank)) {
            $favoritesRank = $this->movie_get_next_favorites_rank() + 1;
        }


        $sql = "INSERT INTO UserToTitleData (email, tconst, favoritesRank) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE favoritesRank=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $tconst, $favoritesRank, $favoritesRank);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Change a title in the user's favorites. */
    public function movie_change_favorites_order($tconst, $favoritesRank)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET watchOrder=? WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("iss", $favoritesRank, $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Remove a title from the user's favorites. */
    public function movie_remove_from_favorites($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET favoritesRank=NULL WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Rate a title. */
    public function movie_add_rating($tconst, $number_of_stars)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO UserToTitleData (email, tconst, number_of_stars) VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE number_of_stars=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $tconst, $number_of_stars, $number_of_stars);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Add a title to the user's favorites. */
    public function movie_change_rating($tconst, $number_of_stars)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET number_of_stars=? WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("iss", $number_of_stars, $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Remove a title from the user's favorites. */
    public function movie_remove_rating($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToTitleData SET number_of_stars=NULL WHERE email=? AND tconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Add a user's rating to a person. */
    public function movie_add_person_rating($nconst, $number_of_stars)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO UserToPersonData (email, nconst, number_of_stars) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE number_of_stars=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $nconst, $number_of_stars, $number_of_stars);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Change a user's rating to a person. */
    public function movie_change_person_rating($nconst, $number_of_stars)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET number_of_stars=? WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("iss", $number_of_stars, $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Remove a user's rating to a person. */
    public function movie_remove_person_rating($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET number_of_stars=NULL WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Add a user's rating to a person. */
    public function movie_add_person_order($nconst, $personOrder)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO UserToPersonData (email, nconst, personOrder) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE personOrder=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ssii", $this->email, $nconst, $personOrder, $personOrder);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Change a user's rating to a person. */
    public function movie_change_person_order($nconst, $personOrder)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET personOrder=? WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("iss", $personOrder, $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** Remove a user's rating to a person. */
    public function movie_remove_person_order($nconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "UPDATE UserToPersonData SET personOrder=NULL WHERE email=? AND nconst=?";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $nconst);
        $statement->execute();
        $statement->close();

        return true;
    }

    public function get_watch_list()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT watchOrder, tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) FROM UserToTitleData NATURAL JOIN Titles as t
        WHERE email=? AND watchOrder IS NOT NULL
        ORDER BY watchOrder ASC;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $watchOrder = null;
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
        $statement->bind_result($watchOrder, $tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes);

        $counter = 1;
        $output = array();
        while ($statement->fetch()) {
            array_push($output, array(
                "watchOrder" => $counter,
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
            $counter++;
        }

        $statement->close();

        return $output;
    }

    public function get_favorites_list()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT favoritesRank, tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) FROM UserToTitleData NATURAL JOIN Titles as t
        WHERE email=? AND favoritesRank IS NOT NULL
        ORDER BY favoritesRank ASC;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $favoritesRank = null;
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
        $statement->bind_result($favoritesRank, $tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes);

        $counter = 1;
        $output = array();
        while ($statement->fetch()) {
            array_push($output, array(
                "favoritesRank" => $favoritesRank,
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
            $counter++;
        }

        $statement->close();

        return $output;
    }

    public function get_rated_movies()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT number_of_stars, tconst, titleType, primaryTitle, originalTitle, isAdult, startYear, endYear, runtimeMinutes, averageRating, numVotes, (SELECT avg(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) as userRating, (SELECT count(number_of_stars) FROM UserToTitleData as ut WHERE ut.tconst = t.tconst) FROM UserToTitleData NATURAL JOIN Titles as t
        WHERE email=? AND number_of_stars IS NOT NULL
        ORDER BY number_of_stars ASC;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $number_of_stars = null;
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
        $statement->bind_result($number_of_stars, $tconst, $titleType, $primaryTitle, $originalTitle, $isAdult, $startYear, $endYear, $runtimeMinutes, $averageRating, $numVotes, $userRating, $numUserVotes);

        $output = array();
        while ($statement->fetch()) {
            array_push($output, array(
                "number_of_stars" => $number_of_stars,
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

    public function count_watch_list()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT count(tconst) FROM UserToTitleData NATURAL JOIN Titles as t WHERE email=? AND watchOrder IS NOT NULL;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    public function count_favorites_list()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT count(tconst) FROM UserToTitleData NATURAL JOIN Titles as t WHERE email=? AND favoritesRank IS NOT NULL;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    public function count_rated_movies()
    {
        // Make sure the user is logged in.
        if (!($this->is_logged_in() || $this->is_initialized())) {
            return false;
        }

        $sql = "SELECT count(tconst) FROM UserToTitleData NATURAL JOIN Titles as t WHERE email=? AND number_of_stars IS NOT NULL;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("s", $this->email);
        $statement->execute();

        $count = null;
        $statement->bind_result($count);
        $statement->fetch();
        $statement->close();

        return $count;
    }

    public function movie_get_rating($tconst)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return null;
        }

        $sql = "SELECT number_of_stars FROM UserToTitleData WHERE email=? AND tconst=?;";

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("ss", $this->email, $tconst);
        $statement->execute();
        $number_of_stars = null;
        $statement->bind_result($number_of_stars);
        $statement->fetch();
        $statement->close();

        return $number_of_stars;
    }
}

$user = new User();
