<?php
if (!isset($_SESSION)) {
    session_start();
}

require 'connection.php';

if (isset($_SESSION["hash"]) && $_SESSION["status"] === "ok") {
    $hash = $_SESSION["hash"];

    $stmt = $pdo->prepare("SELECT * FROM users_list where hash = ?");
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    if ($stmt->execute([$hash])) {
        $data = [];
        $result = $pdo->prepare("SELECT * FROM todo_list");
        $result->execute();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if ($row["checked"] === 0) {
                $row["checked"] = false;
            }
            if ($row["checked"] === 1) {
                $row["checked"] = true;
            }
            $data[] = $row;
        }

        $data = ["items" => $data];
        echo json_encode($data);
    }
}

else {
    $message = ["error" => "401 Unauthorized"];
    die (json_encode($message));
}
?>