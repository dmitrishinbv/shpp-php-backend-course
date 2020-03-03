<?php
require_once 'connection.php';

define ("DATA_TABLE_NAME", "todo_list");
define ("USERS_TABLE_NAME", "users_list");
define ("MAX_LOGIN_SYMBOLS", 40);
define ("MAX_PASS_SYMBOLS", 30);

$items = "CREATE TABLE IF NOT EXISTS ".DATA_TABLE_NAME."(
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
 text text,
 checked INT(1) DEFAULT 0)
 ENGINE=MyISAM DEFAULT CHARSET=utf8";

if (!mysqli_query($conn, $items)) {
    echo "<br>"."Error: " . $items . "<br>" . mysqli_error($conn);
}

$users = "CREATE TABLE IF NOT EXISTS  ".USERS_TABLE_NAME."(
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
login VARCHAR(".MAX_LOGIN_SYMBOLS.") NOT NULL, 
pass VARCHAR(".MAX_PASS_SYMBOLS.") NOT NULL DEFAULT '',
hash text) 
ENGINE=MyISAM DEFAULT CHARSET=utf8";

if (!mysqli_query($conn, $users)) {
    echo "<br>"."Error: " . $users . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);

?>