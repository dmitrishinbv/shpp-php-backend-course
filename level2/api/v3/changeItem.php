<?php
if(!isset($_SESSION)){
    session_start();
}

$errors = ["Error 500. Internal Server Error",
    "Error 400. Bad Request",
    "Error. Such entry not found!"];

// $json = file_get_contents("php://input");

if  (!file_get_contents("input.json")) {
    echo json_encode(["error" => "$errors[0]"]);
    exit();
}

$inputData = file_get_contents("input.json");
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

        if (!is_numeric($id) || !is_string($text) || !is_bool($flag)) {
            echo json_encode(["error" => "$errors[1]"]);
            exit();
        }

    } else {
       echo json_encode(["error" => $_SESSION["error"] ]);
       exit();
    }

    require_once 'connection.php';
    $found = $conn->query("SELECT 'id' FROM todo_list WHERE `id`= '$id'");


    if (mysqli_num_rows($found) <= 0) {
        echo json_encode(["error" => "$errors[2]"]);
        exit();
    }

    $result = $conn->query("UPDATE todo_list SET `text` =  '$text',  `checked` =  '$flag'  WHERE `id`= '$id'");
    echo json_encode(["ok" => true]);

mysqli_close($conn);
?>