<?php

require 'connection.php';
$result = $conn->query("SELECT * FROM todo_list", MYSQLI_STORE_RESULT);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

$data = ["items" => $data];
echo json_encode($data);

mysqli_close($conn);
?>