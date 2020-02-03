<?php
header ("Content-Type: text/html; image/png; charset=utf-8");
$fileName = "./countbd";

if (!file_exists($fileName)) {
    file_put_contents("$fileName", 0);
}

$counter = (int) file_get_contents("$fileName");

if (isset ($_POST['click'])) {
    file_put_contents("$fileName", ++$counter);
    echo " <h3 align=\"center\">You pressed this button $counter times</h3> ";
}

if (isset ($_POST['reset'])) {
    file_put_contents("$fileName", 0);
    echo " <h3 align=\"center\">You pressed this button 0 times</h3> ";
}

echo <<<HTML
 <head>
  <title>PHP Clicker</title>
 </head>
 <body>
 <form method="post">
<p align="center"><input type="submit" name="click" style="text-align: center; color:#8B0000; font-weight:bold;
background-color:#AFEEEE;" value="Click me please"></p>
<p align="center"><input type="submit" name="reset" style="text-align: center; color:#8B0000; font-weight:bold; 
  background-color:#AFEEEE;" value="Reset counter!"></p>
  </form>
 </body>
HTML;

?>