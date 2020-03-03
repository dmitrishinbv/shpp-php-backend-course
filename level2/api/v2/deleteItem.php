<?php
if(!isset($_SESSION)){
    session_start();
}

$errors = ["Error 500. Internal Server Error",
    "Error 400. Bad Request",
    "Error. Such entry not found!"];

if  (!file_get_contents("input.json")) {
    echo json_encode(["error" => "$errors[0]"]);
    exit();
}

$inputData = file_get_contents("php://input");
$inputData = json_decode($inputData, true);


require_once 'connection.php';

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {

       if (!isset ($inputData["id"]) || !is_numeric($inputData["id"])) {
           echo json_encode(["error" => "$errors[1]"]);
           exit();
       }
           $id = $inputData ["id"];
           $result = $conn->query("DELETE FROM todo_list WHERE `id`= '$id'");

    if (!$result) {
        echo json_encode(["error" => "$errors[2]"]);
        exit();
    }

    else {
        echo json_encode(["ok" => true]);
    }
}

else {
    echo json_encode(["error" => $_SESSION["error"] ]);
}
mysqli_close($conn);
?>