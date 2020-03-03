<?php
session_start();
$inputData = file_get_contents('php://input');

//$inputData = file_get_contents('insertItem.json');
//$inputData = file_get_contents('user.json');
$action = $_GET["action"] ?? "";
//$uri = "http://localhost/api/v3/";

$_SESSION['data'] = $inputData;
cors();

switch ($action) {
    case "register" :
        require "register.php";
        break;
    case "login" :
        require "login.php";
        break;
    case "logout" :
        require "logout.php";
        break;
    case "getItems" :
        require "getItems.php";
        break;
    case "addItem" :
        require "addItem.php";
        break;
    case "changeItem" :
        require "changeItem.php";
        break;
    case "deleteItem" :
        require "deleteItem.php";
        break;
     default :
        showError("400 Bad Request");
}



function cors() {

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, HEAD, OPTIONS, PUT, DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }
}


function showError($message) {
    header("HTTP/1.1 $message");
    echo json_encode(['error' => $message]);
    exit();
}

?>