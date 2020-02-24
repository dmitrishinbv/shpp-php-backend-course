<?php
$host = "localhost:3307"; 
$user = "root"; 
$password = "admin"; 
$database = "todo";

$conn = mysqli_connect($host, $user, $password, $database) or die
("<br>" . "Connection failed: " . mysqli_connect_error()) . "<br>";
?>