<?php

abstract class Page
{

    function __construct($param = null){}

    /**
     ** Execute before any HTML is loaded.
    **/
    public function preload(){}

    
    /**
     ** Page specific contents of the <head> tag
    **/
    public function head(){}

    /**
    ** Page specific contents of the <body> tag.
    **/
    public function body(){}
}

?>