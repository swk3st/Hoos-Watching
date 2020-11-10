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

    /**
     * Initialize the current user.
     */
    public function __construct()
    {
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
        }

        unset($email);
        unset($password);
    }

    /**
     * Return whether or not the current user is logged in.
     * 
     * @return bool True if there is a logged in user, false otherwise.
     */
    public function is_logged_in()
    {
        return !(is_null($this->email) || is_null($this->password));
    }

    /**
     * Get the email for the current user.
     * 
     * @return str Email of current user.
     */
    public function get_email()
    {
        if (!$this->is_logged_in()) {
            return null;
        }
        return $this->email;
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
        if (!$this->is_logged_in()) {
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

        $sql = "INSERT INTO Comment(tconst, email, text, likes) VALUES (?, ?, ?, 0)";
        $text_cleaned =  htmlspecialchars($text);

        global $db;

        $statement = $db->prepare($sql);
        if (!$statement) {
            return false;
        }
        $statement->bind_param("sssi", $tconst, $email, $text_cleaned, $likes);
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

    /**
     * Add a movie to the current user's watch list.
     * 
     * @param str $tconst The title identifier.
     * @param int $watch_order The order to put the movie on the list.
     * @return bool True if the operation succeeds, otherwise false if it fails.
     */
    public function movie_add_to_watch_list($tconst, $watch_order)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
        }

        $sql = "INSERT INTO UserToTitleData (email, tconst, watchOrder, date_added) VALUES (?, ?, ?, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE watchOrder=?";

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
    public function movie_add_to_favorites($tconst, $favoritesRank)
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
            return false;
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

    /** Add a title to the user's favorites. */
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

        $sql = "UPDATE UserToTitleData SET watchOrder=? WHERE email=? AND tconst=?";

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

    public function get_watch_list()
    {
        // Make sure the user is logged in.
        if (!$this->is_logged_in()) {
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
}

$user = new User();