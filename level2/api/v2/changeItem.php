<?php
if (!isset($_SESSION)) {
    session_start();
}

$errors = ["500 Internal Server Error", "400 Bad Request"];

//$inputData = file_get_contents("input.json");
$inputData = file_get_contents("php://input");
$inputData = json_decode($inputData, true);

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {

    if (isset($inputData["id"]) and isset($inputData["text"]) and isset($inputData["checked"])) {
        $id = $inputData ["id"];
        $text = $inputData ["text"];
        $flag = $inputData ["checked"];

    } else {
        header($errors[1]);
        echo json_encode(["error" => "$errors[1]"]);
        exit();
    }

var_dump($flag);
    if (!is_numeric($id) || !is_string($text) || !is_bool(boolval($flag))) {
        echo json_encode(["error" => "$errors[1]"]);
        exit();
    }

} else {
    echo json_encode(["error" => $_SESSION["error"]]);
    exit();
}

require 'connection.php';
$found = $conn->query("SELECT `id` FROM todo_list WHERE `id`= '$id'");

if (mysqli_num_rows($found) <= 0) {
    echo json_encode(["error" => "$errors[1]"]);
    exit();
}

if (!$flag) {
    $flag = "0";
}


$result = $conn->query("UPDATE todo_list SET `text` =  '$text',  `checked` =  '$flag'  WHERE `id`= '$id'");
header("HTTP/1.1 200 OK");
echo json_encode(["ok" => true]);

mysqli_close($conn);
?>