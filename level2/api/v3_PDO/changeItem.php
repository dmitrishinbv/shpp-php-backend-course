<?php
if (!isset($_SESSION)) {
    session_start();
}

$errorStatuses = ["Error 500. Internal Server Error", "400 Bad Request", "401 Unauthorized"];

if (!isset($_SESSION['data'])) {
    getResult($errorStatuses[0]);
}

$inputData = $_SESSION['data'];
$inputData = json_decode($inputData, true);

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {
    if (isset($inputData["id"]) and isset($inputData["text"]) and isset($inputData["checked"])) {
        $id = $inputData ["id"];
        $text = $inputData ["text"];
        $flag = $inputData ["checked"];

    } else {
        getResult($errorStatuses[1]);
    }

    if (!$flag) {
        $flag = "0";
    }

    if (!is_numeric((int)$id) || !is_string($text) || !is_bool(boolval($flag))) {
        getResult($errorStatuses[1]);
    }

    require 'connection.php';
    $stmt = $pdo->prepare("SELECT * FROM todo_list where id = ?");
    $exists = false;

    if ($stmt->execute([$id])) {
        $row = $stmt->fetch();
        if ($row) {
            $exists = true;
            $changeEntry = $pdo->prepare("UPDATE todo_list SET text = ?, checked = ? WHERE id = ?");
            $changeEntry->execute([$text, $flag, $id]);
            getResult("ok");
        }

        if (!$exists) {
            getResult($errorStatuses[1]);
        }
    }

} else {
    getResult($errorStatuses[2]);
}


function getResult($message)
{
    if ($message === "ok") {
        header("HTTP/1.1 200 OK");
        echo json_encode(["ok" => true]);

    } else {
        $result = ["error" => $message];
        $_SESSION["error"] = $message;
        die (json_encode($result));
    }
}
?>