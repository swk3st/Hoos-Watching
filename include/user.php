<?php

require_once("db_interface.php");

class User
{
    private $email = null;
    private $password = null;

    /**
     * Initialize the current user.
     */
    public function __construct()
    {
        if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
            return;
        }
        $email = $_SESSION['email'];
        $password = $_SESSION['password']; // This is a bad practice.

        if (login_user($email, $password)) {
            $this->email = $email;
            $this->password = $password;
        }
    }

    /**
     * Return whether or not the current user is logged in.
     */
    public function is_logged_in()
    {
        return !(is_null($this->email) || is_null($this->password));
    }

    public function get_email()
    {
        if (!$this->is_logged_in()) {
            return null;
        }
        return $this->email;
    }

    /**
     * Remove a friend from the current user's friends.
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
        $statement->bind_param("ss", $this->email, $email);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Add a friend to the current user's friends.
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
        $statement->bind_param("ss", $this->email, $email);
        $statement->execute();
        $statement->close();

        return true;
    }

    /**
     * Get an array of all of the user's friends.
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
        $statement->bind_param("sssi", $tconst, $email, $text_cleaned, $likes);
        $statement->execute();
        $statement->close();

        return true;
    }

    /** 
     * Create a public comment on a specific title.
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
        $statement->bind_param("sss", $tconst, $email, $likes);
        $statement->execute();
        $statement->close();

        return true;
    }
}

$user = new User();
