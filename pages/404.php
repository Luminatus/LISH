<?php
defined("BASEPATH") or exit("No direct scripting allowed");
!defined("PAGECLASS") or exit("Only one page class may be loaded at the same time");

define("PAGECLASS", "ErrorPage");
require_once("page.php");

class ErrorPage extends Page
{
    private $link;

    function __construct($param = null)
    {
        if($param == null)
            return;
        if(is_string($param))
            $this->link = $param;
        else if(is_array($param) && array_key_exists('link'))
            $this->link = $param['link'];        
    }
    
    /**
     ** Execute before any HTML is loaded.
    **/
    public function preload(){

    }


    /**
     ** Page specific contents of the <head> tag
    **/
    public function head(){
         ?>   <script>
            function backToMenu()
            {    
                window.location.replace('<?= BASEPATH ?>');
            }
            </script>
            <?php
    }

    /**
    ** Page specific contents of the <body> tag.
    **/
    public function body(){
        ?>
        <h1>404</h1>
        <p>The given shortlink <span style="font-weight: bold; font-style: italic;"><?= $this->link?></span> does not exist.</p>
        <input type="button" value="Return to main page" onclick="backToMenu()">
        <?php
    }

}
?>