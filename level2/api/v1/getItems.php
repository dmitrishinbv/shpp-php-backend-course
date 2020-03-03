<?php
$dataFileName = 'todos.json';
$errorStatus = ["500 Internal Server Error", "502 Bad Gateway"];

if (!file_exists($dataFileName) || !is_readable($dataFileName)) {
    header("HTTP 1.1 $errorStatus[0]");
    die(json_encode(["error" => $errorStatus[0]]));
}

$data = json_decode(file_get_contents($dataFileName), true);

if (!isset($data["items"])) {
    header("HTTP/1.1 $errorStatus[1]");
    die(json_encode(["error" => $errorStatus[1]]));
}

header("HTTP/1.1 200 OK");
echo json_encode($data, true);
?>