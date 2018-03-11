<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
** LI.SH Model class for communicating with the SQLite3 database
*/
class Model
{
    private $db;

    function __construct()
    {
        $this->db = new SQLite3('lish.db');
    }

    /**
    ** Checks if the shorthand link stored in $link is available.
    ** @param $link - shortlink code
    ** @return URL associated with $link if it exists, FALSE otherwise.
    **/
    public function get_full_link($link)
    {
        $query = $this->db->prepare("SELECT full_url, short_url FROM links WHERE short_url = :link");
        $query->bindValue(':link', $link);
        if($cursor = $query->execute())
        { 
            $result = $cursor->fetchArray(SQLITE3_ASSOC);
            return $result;
        }
        else
        {
            return FALSE;
        }
    }

    /**
    ** Inserts a new url/code pair into the database.
    ** @param $full_link - the URL where the shortlink code will redirect.
    ** @param $short_link - custom code entered by the user.
    ** @return $success - TRUE if the query was successful, FALSE otherwise.
    */
    public function insert($full_link, $short_link)
    {
       $query = $this->db->prepare("INSERT INTO links(full_url, short_url) VALUES(:full, :short)");       
       $success = 
                $query->bindValue(':full', $full_link) &&
                $query->bindValue(':short', $short_link) &&
                $query->execute();
       return $success;
    }

    /**
    ** Queries all URL/code pairs from the database
    ** @return array containing the URL/code pairs.
    */
    public function list_all()
    {
        $result = [];
        $cursor = $this->db->query("SELECT full_url, short_url FROM links");
        while($row = $cursor->fetchArray(SQLITE3_ASSOC))
        {
            $result[] = $row;
        }
        return $result;
    }

}

?>