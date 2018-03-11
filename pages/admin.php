<?php
defined("BASEPATH") or exit("No direct scripting allowed");
!defined("PAGECLASS") or exit("Only one page class may be loaded at the same time");

define("PAGECLASS", "AdminPage");
require_once("page.php");

class AdminPage extends Page
{    
    /**
     ** Execute before any HTML is loaded.
    **/
    public function preload(){
    }
    
    /**
     ** Page specific contents of the <head> tag
    **/
    public function head(){
        ?>
        <script src="static/js/admin.js"></script>
        <link rel="stylesheet" type="text/css" href="<?=BASEPATH?>/static/css/admin.css"/>
        <?php
    }
    
    /**
    ** Page specific contents of the <body> tag.
    **/
    public function body(){     
        ?>             
            <h3>Admin page</h3>
        <div id="error-container"></div>
            <form id="admin-form" class="col-xs-6">
                <p>Please enter the admin's password</p>
                <div class="input-field row"><label class="col-xs-4">Password</label><input class="col-xs-8" type="password" name="admin-pw"></div>
                <button id="submit-button" type="submit">Login</button>
            </form>
            <div class="col-xs-12"><img width="50px" src="<?=BASEPATH?>/static/image/loading.gif" id="loading"></div>
            <div class="col-xs-12" id="result-container">                
            </div>
            
        <?php

    }

}
?>