<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once("model.php");

/**
** LI.SH API for opening and creating shortlinks.
** All public API methods return an associative $result array with a 'status' and 'response' property.
** $result['status'] holds the HTTP status code for the response
** $result['response'] holds the response message
** API documentation only details $result['response'] as the return value.
*/
class API{
    
    
    //Admin password hash that is cross-checked for admin-level API calls.
    private static $ADMIN_HASH = '$2y$10$muQn/LsTicdzSAi4R6nuEuwy06ofQj3sLdlC5y8nBL8P35p.9ClGS';

    /**
    ** Short url codes that may not be used for effective or security reasons.
    ** For Php 5 < 5.6.0, use a static method instead of const array.
    */
    const FORBIDDEN_URLS = ["api", "admin", "index", "pages", "account", "register", "login", "main"];

    private $model;

    function __construct(){        
        $this->model = new Model();
    }

    /**
    ** Authentication for admin-level API calls.
    ** @param $pw - the password entered by the user.
    */
    private function adm_authenticate($pw)
    {
        return password_verify($pw, self::$ADMIN_HASH);
    }

    /**
    ** Checks the availability of $link as a shorthand URL.
    ** @param $link - the shortlink code to be checked
    ** @return true, if $link is available, false otherwise 
    **/
    public function check_get($link)
    {
        $result = ['status' => 400, 'response' => null];
        if($this->check_shortlink_format($link))
        {
            if(array_search(strtolower($link), self::FORBIDDEN_URLS)===FALSE)
            {
                if(!$link = $this->model->get_full_link($link))
                {
                    $result['response'] = TRUE;
                    $result['status'] = 200;
                }
                else
                {
                    $result['response'] = FALSE;
                    $result['status'] = 200;
                }                
            }
            else
            {
                $result['response'] = "The word '$link' is forbidden, and cannot be used as shortlink";
                $result['status'] = 400;
            }
        }
        else
        {
            $result['response'] = "Wrong shortlink format for $link";
            $result['status'] = 400;
        }
        return $result;
    }

    /**
    ** Returns the URL associated with the shorthand URL stored in $link.
    ** @param $link - shortlink code to open
    ** @return full URL associated with $link if it exists, error message otherwise.
    **/
    public function open_get($link)
    {
        $result = ['status' => 400, 'response' => null];
        if($this->check_shortlink_format($link))
        {
            if($full_link = $this->model->get_full_link($link))
            {
                $result['response'] = $full_link;
                $result['status'] = 200;
            }
            else
            {
                $result['response'] = "Shortlink '$link' does not exist";
                $result['status'] = 409;
            }
        }
        else
        {
            $result['response'] = "Wrong shortlink format for $link";
            $result['status'] = 400;
        }

        return $result;
    }

    /**
    ** Creates a new shorthand link pair
    ** Parameters are sent via the $_POST array.
    ** @param 'short_url' - the custom code entered by the user
    ** @param 'full_url' - the URL associated with the given code.
    ** @return The saved URL/code pair on success, error message on fail
    ** NOTE: the returned url might not be the same as the entered url
    **/
    public function create_post()
    {
        $result = ['status' => 400, 'response' => null];
        $required = ['short_url', 'full_url'];
        $errors = [];
        foreach($required as $req)
        {
            if(!array_key_exists($req, $_POST) || empty($_POST[$req]))
                $errors[] = ["Parameter $req is missing"];
        }
        if(count($errors))
        {
            $result['response'] = $errors;
        }
        else
        {
            $full_url = $_POST['full_url'];
            $short_url = $_POST['short_url'];
            if($this->check_shortlink_format($short_url))
            {
                if(array_search(strtolower($short_url), self::FORBIDDEN_URLS)===FALSE )
                {
                    if($full_url = $this->get_valid_url($full_url))
                    {
                        if(!$this->model->get_full_link($short_url))
                        {
                            if($this->model->insert($full_url, $short_url))
                            {
                                $result['status'] = 201;
                                $result['response'] = ['full_url' => $full_url, 'short_url' => $short_url];                            
                            }
                            else
                            {
                                $result['status'] = 500;
                                $result['response'] = "An error occured during saving";
                            }
                        }
                        else
                        {
                            $result['response'] = "Shortlink '$short_url' is already taken";
                            $result['status'] = 409;
                        }
                    }
                    else
                    {
                        $result['status'] = 500;
                        $result['response'] = "The given URL does not lead to an existing site";
                    }
                }
                else
                {                    
                    $result['response'] = "The word '$short_url' is forbidden, and cannot be used as shortlink";
                    $result['status'] = 400;
                }
            }
            else
            {
                $result['response'] = "Wrong shortlink format for $short_url";
            }

        }
        return $result;
    }

    /**
    ** Admin-level method for querying all url entries.
    ** @return all URL pairs from the database    
    */
    public function list_all_post()
    {
        $result = ['response' => 'No authorization given', 'status' => 400];
        if(array_key_exists('pw', $_POST))
        {
            if($this->adm_authenticate($_POST['pw']))
            {
                if($result_array = $this->model->list_all())
                {
                    $result['response'] = $result_array;
                    $result['status'] = 200;
                }
                else
                {
                    $result['response'] = "An error occured while fetching the data";
                    $result['status'] = 500;
                }
            }
            else
            {
                $result['response'] = 'You are not authorized to use this method';
                $result['status'] = 401;
            }
        }

        return $result;
    }

    /**
    **  Checks whether URL leads to an existing site, and returns it in valid format.
    ** @return entered url in valid url form, if it exists, false otherwise.
    */
    private function get_valid_url($url=null)
    {
        if($url == null)
            return false;
        $prefixes = ['','http://', 'http://www.']; //http://www. might be unnecessary.
        $return = false;
        $i = 0;
        while($i<count($prefixes) && !$return)
        {
            $prefix = $prefixes[$i];
            $file = $prefix.$url;
            $file_headers = @get_headers($file);
            if($file_headers && $file_headers[0] != 'HTTP/1.1 404 Not Found') {
                $return = $file;
            }
            $i++;
        }

        return $return;
    }

    /**
    ** Checks whether the code stored in $link is in correct format
    ** A valid code may contain english letters (upper-, and lowercase), numbers, and the '_' symbol
    ** The code must not consist of only '_' symbols.
    ** @param $link - the short code enetered by the user
    ** @return true if code is in correct format, false otherwise
    */
    private function check_shortlink_format($link)
    {        
        return preg_match('/^[a-z0-9_]*[a-z0-9][a-z0-9_]*$/i',$link);        
        
    }
}

?>