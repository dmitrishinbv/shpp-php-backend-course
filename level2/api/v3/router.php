<?php
session_start();
//$inputData = json_decode(file_get_contents('php://input'), true);

$inputData = file_get_contents('input.json');
$action = $_GET["action"] ?? "";
$uri = "http://localhost/api/v3/";

switch ($action) {
    case "register" :
        curl($inputData, $uri."register.php");
        break;
    case "login" :
        curl($inputData, $uri."login.php");
        break;
    case "logout" :
        curl("", $uri."logout.php");
        break;
    case "getItems" :
        isAuthorized() ? curl("", $uri . "getItems.php") : showError("401 Unauthorized");
        break;
    case "addItem" :
        isAuthorized() ? curl($inputData, $uri . "addItem.php") : showError("401 Unauthorized");
        break;
    case "changeItem" :
        isAuthorized() ? curl($inputData, $uri . "changeItem.php") : showError("401 Unauthorized");
        break;
    case "deleteItem" :
        isAuthorized() ? curl($inputData, $uri . "deleteItem.php") : showError("401 Unauthorized");
        break;
     default :
        showError("400 Bad Request");
}


function curl ($inputData, $url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    if ($inputData !== "") {
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($inputData)
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $inputData);
    }
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    echo curl_exec($curl);
    curl_close($curl);
}

function isAuthorized () {
    require 'connection.php';

if (isset($_SESSION["hash"])) {
    $hash = $_SESSION["hash"];
    echo "router ";
    var_dump($_SESSION["hash"]);
    $found = $conn->query("SELECT * FROM users_list WHERE `hash`= '$hash'");
    $found = mysqli_fetch_array($found);

    if ($found !== null && $found["id"]) {
    return true;
    }
}
return false;
}

function showError($message) {
    header("HTTP/1.1 $message");
    echo json_encode(['error' => $message]);
    exit();
}

?>