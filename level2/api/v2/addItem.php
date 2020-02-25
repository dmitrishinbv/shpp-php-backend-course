<?php
session_start();
require_once 'connection.php';
//$inputData = $_SESSION['data'];
$inputData = file_get_contents("input.json");
$inputData = json_decode($inputData, true);

if (isset($_SESSION["status"]) && $_SESSION["status"] === "ok") {
    $conn = mysqli_connect($host, $user, $password, $database) or die
    ("<br>" . "Connection failed: " . mysqli_connect_error()) . "<br>";

    if (isset($inputData["text"])) {
        $text = $inputData["text"];
         $query = mysqli_query($conn, "INSERT INTO todo_list (text) VALUES ('$text')");
        $id = mysqli_insert_id($conn);

    if (!$id !== 0) {
        echo json_encode(["id" => $id]);

    } else {
        echo json_encode(["error" => "Sorry, it is not possible to add this record to the table!"]);
    }
}
    else {
        $data = ["error" => "Error 400. Bad Request"];
        echo json_encode($data);
    }
}

else {
    $data = ["error" => "Log in please!"];
    echo json_encode($data);
}

mysqli_close($conn);
?>