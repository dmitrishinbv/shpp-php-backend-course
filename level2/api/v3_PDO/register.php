<?php

define("MIN_PASS_SYMBOLS", 3);
define("MAX_PASS_SYMBOLS", 30);
define("MAX_LOGIN_SYMBOLS", 40);
require 'connection.php';

$userInfo = $_SESSION['data'];
$userInfo = json_decode($userInfo, true);

$errors = ["Error 500. Internal Server Error",
    "Error 400. Bad Request",
    "Error. User with this name is already registered!",
    "Error. Password length is incorrect! The password length must be from " . MIN_PASS_SYMBOLS .
     " to " . MAX_PASS_SYMBOLS . " symbols",
    "Error. Max login length is " . MAX_LOGIN_SYMBOLS . " symbols"];


if (count($userInfo) !== 2 or !isset($userInfo["login"]) or !isset($userInfo["pass"])) {
    header("HTTP/1.1 .'$errors[1]'");
    die (json_encode(["error" => "$errors[1]"]));
}

$login = $userInfo ["login"];
$pass = $userInfo ["pass"];

if (strlen($pass) < MIN_PASS_SYMBOLS or strlen($pass) > MAX_PASS_SYMBOLS) {
    header("HTTP/1.1 .'$errors[3]'");
    die (json_encode(["error" => "$errors[3]"]));
}


checkUserInfo($login, $pdo, $errors);
$hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users_list (login, pass, hash) VALUES (:name, :pass, :hash)");

$stmt -> bindParam(':name', $login);
$stmt -> bindParam(':pass', $pass);
$stmt -> bindParam(':hash', $hash);
$stmt -> execute();
require 'logout.php';


function checkUserInfo($login, $pdo, $errors)
{
    $stmt = $pdo->prepare("SELECT * FROM users_list where login = ?");

    if ($stmt->execute([$login])) {
        $row = $stmt->fetch();
        if($row) {
            header("HTTP/1.1 .'$errors[2]'");
            echo json_encode(["error" => "$errors[2]"]);
            exit();
        }
    }

    if (strlen($login) > MAX_LOGIN_SYMBOLS) {
        header("HTTP/1.1 .'$errors[4]'");
        echo json_encode(["error" => "$errors[4]"]);
        exit();
    }
}
?>