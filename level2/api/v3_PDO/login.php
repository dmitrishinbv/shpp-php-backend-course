<?php
if (!isset($_SESSION)) {
    session_start();
}

$userInfo = $_SESSION['data'];
$errors = ["Error 500. Internal Server Error",
    "Error 400. Bad Request",
    "Error. Such user not found!",
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

$stmt = $pdo->prepare("SELECT * FROM users_list where login = ?");

if ($stmt->execute([$login])) {
    $exists = true;
    $row = $stmt->fetch();
    $found_id = $row["id"];
    $found_login = $row["login"];
    $found_pass = $row["pass"];
    $found_hash = $row["hash"];
}

if (!$exists) {
    header("HTTP/1.1 .'$errors[2]'");
    echo json_encode(["error" => "$errors[2]"]);
    exit();
}

if ($found_pass !== $pass) {
    header("HTTP/1.1 .'$errors[3]'");
    echo json_encode(["error" => "$errors[3]"]);
    exit();
}

$hash = md5(generateCode(50));
$_SESSION['hash'] = $hash;

$stmt = $pdo->prepare("UPDATE users_list SET hash = ? WHERE login = ?");
$stmt->execute([$hash, $login]);

setcookie("user_id", $found_id, time() + 30 * 24 * 60 * 60);
$_COOKIE['user_id'] = $found_id;
$session_id = generateSessionId(8);
setcookie("session_id", $session_id, time() + 30 * 24 * 60 * 60);
$_COOKIE['session_id'] = $session_id;

include "check.php";


function generateCode($length)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $codeLength = strlen($chars) - 1;

    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $codeLength)];
    }

    return $code;
}


function generateSessionId($length)
{
    $chars = "0123456789";
    $code = "";
    $codeLength = strlen($chars) - 1;

    while (strlen($code) < $length) {
        $code .= $chars[mt_rand(0, $codeLength)];
    }

    return (int)$code;
}
?>