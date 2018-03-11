<?php

/**index.php
** Every request (except for resource requests from the static directory) is redirected here.
** Request url is processed and the correct method is called accordingly.
*/

require_once("input.php");
define('BASEPATH', get_basepath()); //BASEPATH - absolute path to root directory containing this index.php

$uri_array = process_url();

/*Selecting main method based on the first element in $uri_array (or the lack of one if empty)*/
if(count($uri_array) > 0)
{
    switch($uri_array[0])
    {
        case 'api': call_api_method(array_slice($uri_array, 1)); break;
        case 'admin': load_page('pages/admin.php'); break;
        default: open_link($uri_array[0]); break;

    }
}
else
{
    load_page('pages/main.php');
}
die();


/**
** Selects and executes the correct API method based on the parameter list in $uri_params
** @param $uri_params, the first index contains the method name, the subsequent indexes hold the parameter list.
** @return prints the response of the API method (or an error message), and sets the HTTP status accordingly.
*/
function call_api_method($uri_params)
{
    $result = ['status' => 400, 'response'=>null];
    if(count($uri_params) < 1)
    {
        $result['response'] = "No method called!";   
    }
    else
    {
        $method = $uri_params[0];
        $params = array_slice($uri_params, 1);

        include("api.php");
        $method_name = $method."_".strtolower($_SERVER['REQUEST_METHOD']); 
        $api = new API();
        if(method_exists($api,$method_name))
        {
            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                $reflection = new ReflectionMethod($api, $method_name);
                $param_num = $reflection->getNumberOfParameters();
                if(count($params)<$param_num)
                {
                    $result['response'] = "Insufficient numer of parameters for $method_name, expecting $param_num!";
                }
                else
                {
                    $result = call_user_func_array([$api,$method_name], $params);
                   
                }
            }
            else
            {
                $result = call_user_func([$api,$method_name]);
            }
        }
        else
        {
            $result['response'] = "Invalid method '$method_name'!";
        }
    }

    http_response_code($result['status']);
    print(json_encode($result['response']));
}


/**
** Opens the full link associated to the code stored in $link
** @param $link containing the short code associated with a full url
** @return redirects to the url associated with $link, or the 404 error page if opening the link fails.
*/
function open_link($link)
{
    include("api.php");
    $api = new API();  
    $result = $api->open_get($link);
    if($result['status'] == 200)
    {
        header("Location: ".$result['response']['full_url']);
        die();
    }
    else
    {
        load_page("pages/404.php", $link);
    }
}


/**
** Loads a page from the server using the site frame and the correct Page class.
** @param $path - location of the page to load
** @param $param - optional parameters for the page
*/
function load_page($path, $param = null)
{
    if(is_array($param) || is_scalar($param))
        define("PAGEPARAM", $param); //parameters are set through the PAGEPARAM constant 
    
    //We first include the page to load, then the frame which will initialize the page.
    include($path);
    include("pages/frame.php");
}
?>