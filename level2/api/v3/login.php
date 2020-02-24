<?php
session_start();
//$userInfo = file_get_contents("php://input");
$userInfo = file_get_contents("user.json");
$errors = ["Error 500. Internal Server Error", "Error 400. Bad Request", "Error. Such user not found!",
    "Error. Username and password don't match!"];


if ($userInfo) {
    $userInfo = json_decode($userInfo, true);

    if (count($userInfo) !== 2 or !isset($userInfo["login"]) or !isset($userInfo["pass"])) {
        header("HTTP/1.1 .'$errors[1]'");
        echo json_encode(["error" => "$errors[1]"]);
        exit();
}
        $login = $userInfo ["login"];
        $pass = $userInfo ["pass"];

} else {
    header("HTTP/1.1 .'$errors[0]'");
    echo json_encode(["error" => "$errors[0]"]);
    exit();
}

require 'connection.php';

$found = $conn->query("SELECT * FROM users_list WHERE `login`= '$login'");
$found = mysqli_fetch_array($found);

if (!$found) {
    header("HTTP/1.1 .'$errors[2]'");
    echo json_encode(["error" => "$errors[2]"]);
    exit();
}

if ($found['pass'] !== $pass) {
    header("HTTP/1.1 .'$errors[3]'");
    echo json_encode(["error" => "$errors[3]"]);
    exit();
}

$hash = md5(generateCode(50));
$setHash = $conn->query("UPDATE users_list SET `hash`='$hash' WHERE `login`= '$login'");
$_SESSION['hash']= $hash;
echo "login ";
var_dump($_SESSION['hash']);

if ($found["id"]) {
    setcookie("user_id", $found['id'], time() + 30 * 24 * 60 * 60);
    $_COOKIE['user_id'] = $found['id'];
    $session_id = generateSessionId(8);
    setcookie("session_id", $session_id, time() + 30 * 24 * 60 * 60);
    $_COOKIE['session_id'] = $session_id;
}

    include "check.php";


function generateCode($length) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;

    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }

    return $code;
}


function generateSessionId($length) {
    $chars = "0123456789";
    $code = "";
    $clen = strlen($chars) - 1;

    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0,$clen)];
    }

    return (int)$code;
}

?>