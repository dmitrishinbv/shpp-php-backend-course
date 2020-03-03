<?php
if (!isset($_SESSION)) {
    session_start();
}

$errors = ["Error 500. Internal Server Error", "Error 400. Bad Request", "Error. Such entry not found!"];

if (!isset($_SESSION['data'])) {
    die (json_encode(["error" => "$errors[0]"]));
}
$data = json_decode($_SESSION['data'], true);

include "check.php";

if (!isset ($data["id"]) || !is_numeric((int)$data["id"])) {
    die (json_encode(["error" => "$errors[1]"]));
}
$id = $data ["id"];

require 'connection.php';

$result = $conn->query("DELETE FROM todo_list WHERE `id`= '$id'");

!$result ? die (json_encode(["error" => "$errors[2]"])) : $data = json_encode(["ok" => true]);
header("HTTP/1.1 200 OK");
echo $data;

mysqli_close($conn);
?>