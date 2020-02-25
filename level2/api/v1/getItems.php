<?php
$json = 'todos.json';

$errorText = "Error 500. Internal Server Error";
$error = ["error" => $errorText];

if (!is_readable($json) || !file_exists($json)) {
    header($errorText);
    echo json_encode($error);
    exit();
}

$json = json_decode(file_get_contents($json), true);

if (!is_array($json) || !array_key_exists("items", $json)) {
    header($errorText);
    echo json_encode($error);
    exit();
}

echo json_encode($json, true);
?>