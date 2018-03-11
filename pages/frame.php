<?php
defined("BASEPATH")  or exit("No direct scripting allowed!");
defined("PAGECLASS") or exit("No page to load!");

/*Common HTML frame of every page*/

//Checking whether PAGECLASS contains a valid class derived from base Page class, then initializing it.
$reflection = null;
try
{
    $reflection = new ReflectionClass(PAGECLASS);
}
catch(Exception $e)
{    
    exit($e->getMessage());
}
   
$parent = $reflection->getParentClass();
if($parent && $parent->getName() == "Page")
{
    $params = defined("PAGEPARAM") ? PAGEPARAM : null;
    $page = $reflection->newInstance($params);
}

//Calling page's preload method before loading any HTML
$page->preload();
?>

<!DOCTYPE HTML>
<html>
<head>
<meta name="basepath" content="<?=BASEPATH?>">

<script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="<?=BASEPATH?>/static/css/style.css"/>

	<script src="<?=BASEPATH?>/static/js/script.js"></script>	

<?=$page->head()?>

</head>
<body>
<div class="body-container">
    <div class="container">
        <div class="header row">
            <h1 id="title">LI.SH</h1>
            <h2 id="subtitle">Your everyday Link Shortener</h2> 
        </div>
        <div class="page col-xs-12">
            <div class="content col-xs-12 col-md-10">
                <?=$page->body()?>
            </div>
        </div>
    </div>
    <div class="footer">Created by Richárd Szappanos (2018)</div>
</div>
</body>
</html>