<?php
define("NUMBER_OF_COLUMNS", 3);

$json = 'todos.json';
$input = json_decode(file_get_contents("php://input"), true);
// $input = 'item.json';
$errors = ["Error 500. \"Internal Server Error\"", "Error 400. \"Bad Request\""];

checkInfo($json, $input, $errors);


function checkInfo($json, $input, $errors)
{
    $json = checkJson($json, $errors);

    if ($json && $input) {
        $json = json_decode($json, true);

        if (!is_array($json) || !is_array($input) || count($input) !== NUMBER_OF_COLUMNS
            || !array_key_exists("text", $input) || !array_key_exists("id", $input)
            || !array_key_exists("checked", $input) || !array_key_exists("items", $json)) {
            echo(json_encode(["error" => $errors[1]]));
            exit();
        }

        changeItem($json, $input, $errors);

    } else {
        echo(json_encode(["error" => $errors[0]]));
        exit();
    }

}


function checkJson($json, $errors)
{
    if (is_readable($json) || is_writable($json)) {
        $json = file_get_contents($json);

    } else if (is_readable($json) || !is_writable($json)) {
        echo(json_encode(["error" => $errors[0]]));
        exit();

    } else {
        $json = false;
    }

    return $json;
}


function changeItem($json, $item, $errors)
{
    $itemsArray = $json['items'];
    $id = $item["id"];

    for ($i = 0; $i < count($itemsArray); $i++) {
        $currentItem = $itemsArray[$i];

        if ($currentItem["id"] === $id) {
            $itemsArray[$i] = array("id" => $id, "text" => $item["text"], "checked" => $item["checked"]);
            $itemsArray = array("items" => $itemsArray);
            $data[] = json_encode($itemsArray);
            file_put_contents("todos.json", $data);
            echo(json_encode(["ok" => true]));
            break;
        }

        if ($currentItem["id"] !== $id && $i === count($itemsArray) - 1) {
            echo(json_encode(["error" => $errors[1]]));
            exit();
        }
    }
}

?>