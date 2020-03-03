<?php
define("NUMBER_OF_COLUMNS", 3);

$jsonFileName = 'todos.json';
$input = json_decode(file_get_contents("php://input"), true);
//$input = json_decode(file_get_contents("item.json"), true);
$errorStatuses = ["500 Internal Server Error", "400 Bad Request"];

$data = checkInfo($jsonFileName, $input, $errorStatuses);
changeItem($jsonFileName, $data, $input, $errorStatuses);

function checkInfo($jsonFileName, $input, $errorStatuses)
{
    $data = checkJson($jsonFileName);

    if ($data && $input) {
        $data = json_decode($data, true);

        if (count($input) !== NUMBER_OF_COLUMNS || !isset ($input["text"]) || !isset ($input["id"])
            || !isset ($input["checked"]) || !is_bool(boolval($input["checked"]))) {
            showError($errorStatuses[1]);
        }


    } else {
        showError($errorStatuses[1]);
    }

    return $data;
}


function checkJson($jsonFileName)
{
    if (is_readable($jsonFileName) && is_writable($jsonFileName)) {
        $data = file_get_contents($jsonFileName);

    } else {
        $data = false;
    }

    return $data;
}


function changeItem($jsonFileName, $data, $item, $errorStatuses)
{
    $itemsArray = $data['items'];

    for ($i = 0; $i < count($itemsArray); $i++) {
        $currentItem = $itemsArray[$i];

        if ($currentItem["id"] == $item["id"]) {
            $itemsArray[$i] = array("id" => $item["id"], "text" => $item["text"], "checked" => $item["checked"]);
            $itemsArray = array("items" => $itemsArray);
            $data = json_encode($itemsArray);
            file_put_contents("$jsonFileName", $data);
            echo(json_encode(["ok" => true]));
            exit();
        }

    }
    showError($errorStatuses[1]);
}

function showError($message)
{
    header("HTTP/1.1 $message");
    die (json_encode(['error' => $message]));
}


?>