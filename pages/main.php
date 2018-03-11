<?php
defined("BASEPATH") or exit("No direct scripting allowed");
!defined("PAGECLASS") or exit("Only one page class may be loaded at the same time");

define("PAGECLASS", "MainPage");
require_once("page.php");

class MainPage extends Page
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
        <script src="<?=BASEPATH?>/static/js/main.js"></script>
        <?php
    }


    /**
    ** Page specific contents of the <body> tag.
    **/
    public function body(){
      ?> 
        <div class="introduction">
            <h3>Introduction</h3>
            <p>LI.SH (short for Link Shortener) is an easy to use custom link shortener service. You may attach short codes to any url, and a reusable link will be generated that will redirect you to the chosen url.</p>
        </div>
        <div class="howto">
            <h3>How to use</h3>
            <p>Enter the full url of the page you want to create the shortlink for, then enter a custom alphanumeric code, which will be used as the li.sh shortlink. The following rules apply:</p>
            <ul>
                <li>The short code may only contain letters from the english alphabet, numbers, and the '_' symbol.</li>
                <li>The short code MUST contain at least one letter or number</li>
                <li>The full link MUST lead to an existing site</li>
                <li>Certain words (like login, register, account, etc) are forbidden, and cannod be used as shortlinks.</li>
            </ul>
            
        </div>
        <div id="error-container"></div>
        <form id="link-form" method="POST" action="api/create" class="col-xs-6">
            <div class="input-field row"><label class="col-xs-4">Full URL</label><input class="col-xs-8" type="text" name="url"></div>
            <div class="input-field row"><label class="col-xs-4">Code</label><input class="col-xs-8" type="text" name="code"></div>
            <button id="submit-button" type="submit">Create Shortlink</button>
            <div class="col-xs-12"><img width="50px" src="<?=BASEPATH?>/static/image/loading.gif" id="loading"></div>
            <div class="col-xs-12" id="result-container">
                <span>Your LI.SH shortlink is:</span><a target="_blank" id="result-link"></a>
            </div>
        </form>
     <?php
    }

}
?>