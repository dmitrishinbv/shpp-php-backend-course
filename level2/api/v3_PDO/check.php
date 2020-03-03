<?php
if(!isset($_SESSION)){
    session_start();
}
require 'connection.php';

if (isset($_SESSION['hash']) and isset($_COOKIE['user_id']) and isset($_COOKIE['session_id'])) {
    $id = $_COOKIE['user_id'];
    $hash = $_SESSION['hash'];
    $stmt = $pdo->prepare("SELECT * FROM users_list where id = ?");

    if ($stmt->execute([$id])) {
        $found = true;
        $row = $stmt->fetch();
        $found_hash = $row["hash"];
    }

    if (!$found) {
        getResult("401 Unauthorized");
    }

    if ($hash !== $found_hash) {
        setcookie("user_id", "", time() - 3600 * 24 * 30 * 12, "/");
        setcookie("session_id", "", time() - 3600 * 24 * 30 * 12, "/");
        $response = json_encode(["error" => "Session expired!"]);
    }

    getResult ("ok");

} else {
    getResult("401 Unauthorized");
}


function getResult ($message) {
    if ($message === "ok") {
        header("HTTP/1.1 200 OK");
        $_SESSION["status"] = "ok";
        echo json_encode(["ok" => true]);
    }

    else {
        $result = ["error" => $message];
        $_SESSION["error"] = $message;
        echo json_encode($result);
    }
}
?>