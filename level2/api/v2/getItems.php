<?php
if (!isset($_SESSION)) {
    session_start();
}
require 'connection.php';

if (isset($_SESSION["hash"]) && $_SESSION["status"] === "ok") {
    $hash = $_SESSION["hash"];

    $found = $conn->query("SELECT * FROM users_list WHERE `hash`= '$hash'");
    $found = mysqli_fetch_array($found);

    if ($found !== null && $found["id"]) {
        $result = $conn->query("SELECT * FROM todo_list", MYSQLI_STORE_RESULT);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            if ($row["checked"] == 0) {
                $row["checked"] = false;
            }
            if ($row["checked"] == 1) {
                $row["checked"] = true;
            }
            $data[] = $row;
        }

        $data = ["items" => $data];
        echo json_encode($data);
    } else {
        error();
    }

} else {
    error();
}


function error()
{
    $data = ["error" => "Log in please!"];
    echo json_encode($data);
}

mysqli_close($conn);
?>