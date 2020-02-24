<?php

require 'connection.php';

if (isset($_SESSION['hash']) and isset($_COOKIE['user_id']) and isset($_COOKIE['session_id'])) {
    $id = $_COOKIE['user_id'];
    $hash = $_SESSION['hash'];
    $query = mysqli_query($conn, "SELECT * FROM users_list WHERE `id`= '$id'");
    $userdata = mysqli_fetch_assoc($query);

    if (!$userdata["login"]) {
        $response = ["error" => "Log in please!"];
    }

    if ($hash !== $userdata["hash"]) {
        setcookie("user_id", "", time() - 3600 * 24 * 30 * 12, "/");
        setcookie("session_id", "", time() - 3600 * 24 * 30 * 12, "/");
        $response = ["error" => "Session expired!"];
    }

    $response = ["ok" => true];
    $displayResult = false;

} else {
    $response = ["error" => "Log in please!"];
}

    $_SESSION["status"] = $response;

    if (isset($response["ok"])) {
        echo json_encode($response);
    }
?>