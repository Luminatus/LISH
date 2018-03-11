<?php

/**
** Breaks up the request uri past the root directory into an array.
** @return $uri_array containing the uri in array form.
**/
function process_url()
{
    $path = dirname($_SERVER['SCRIPT_NAME']);
    $trimmed_uri = trim(substr($_SERVER['REQUEST_URI'],strlen($path)),'/');

    //echo $_SERVER['REQUEST_URI'];

    $querypos = strpos($trimmed_uri, '?');
    if($querypos != FALSE)
    {
        $trimmed_uri = substr($trimmed_uri, 0, $querypos);
    }

    $uri_array = $trimmed_uri ? explode('/',$trimmed_uri) : [];
    return  $uri_array;
}

/**
** Builds up an absolute path to the root directory
** @return $basepath containing the absolute path to root.
**/
function get_basepath()
{
    $scheme = $_SERVER['REQUEST_SCHEME'];
    $host = $_SERVER['SERVER_NAME'];
    $path = dirname($_SERVER['SCRIPT_NAME']);

    $basepath = "{$scheme}://{$host}{$path}";
    return $basepath;
}