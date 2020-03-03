<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'connection.php';
$errorStatuses = ["500 Internal Server Error", "400 Bad Request", "401 Unauthorized"];

//$inputData = json_decode(file_get_contents("input.json"), true);
$inputData = $_SESSION['data'];
$inputData = json_decode($inputData, true);

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {

    if (isset($inputData["id"]) and isset($inputData["text"]) and isset($inputData["checked"])) {
        $id = $inputData ["id"];
        $text = $inputData ["text"];
        $flag = $inputData ["checked"];

    } else {
        error($errorStatuses[1], $conn);
    }

    if (!is_numeric((int)$id) || !is_string($text) || !is_bool($flag)) {
        error($errorStatuses[1], $conn);
    }

    $found = $conn->query("SELECT 'id' FROM todo_list WHERE `id`= '$id'");
    $found = mysqli_fetch_array($found);

    if (!$found) {
        error($errorStatuses[1], $conn);
    }
}

else {
    error($errorStatuses[2], $conn);
}


$result = $conn->query("UPDATE todo_list SET `text` =  '$text',  `checked` =  '$flag'  WHERE `id`= '$id'");
echo json_encode(["ok" => true]);
mysqli_close($conn);

function error ($status, $conn) {
    mysqli_close($conn);
    $message = ["error" => $status];
    die (json_encode($message));
}
?>