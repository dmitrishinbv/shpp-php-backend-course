<?php
if(!isset($_SESSION)){
    session_start();
}

require 'connection.php';
$errorStatuses = ["500 Internal Server Error", "400 Bad Request", "401 Unauthorized"];

//$inputData = json_decode(file_get_contents("input.json"), true);
$inputData = $_SESSION['data'];
$inputData = json_decode($inputData, true);

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {

    if (isset($inputData["text"])) {
        $text = $inputData["text"];
        $query = mysqli_query($conn, "INSERT INTO todo_list (text) VALUES ('$text')");
        $id = mysqli_insert_id($conn);

        if (!$id !== 0) {
            header("HTTP/1.1 200 OK");
            echo json_encode(["id" => $id]);

        } else {
            die (json_encode(["error" => "Sorry, it is not possible to add this record to the table!"]));
        }
    }

    else {
        header("HTTP/1.1 .'$errorStatuses[1]'");
        $data = ["error" => $errorStatuses[1]];
        die (json_encode($data));
    }
}

else {

    header("HTTP/1.1 .'$errorStatuses[2]'");
    $data = ["error" => $errorStatuses[2]];
    die (json_encode($data));
}

mysqli_close($conn);
?>