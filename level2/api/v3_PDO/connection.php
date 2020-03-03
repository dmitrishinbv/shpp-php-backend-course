<?php
$host = "localhost:3307"; 
$user = "root"; 
$password = "admin"; 
$database = "todo";

$pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);

//$conn = mysqli_connect($host, $user, $password, $database) or die
//("<br>" . "Connection failed: " . mysqli_connect_error()) . "<br>";
?>