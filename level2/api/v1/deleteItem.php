<?php
$input = json_decode(file_get_contents("php://input"), true);

$jsonFileName = 'todos.json';
//$input = json_decode(file_get_contents('item.json'), true);
$errorStatuses = ["500 Internal Server Error", "400 Bad Request"];

$data = checkInfo($jsonFileName, $input, $errorStatuses);
deleteItem($jsonFileName, $data, $input, $errorStatuses);

function checkInfo($jsonFileName, $input, $errorStatuses)
{
    $data = checkJson($jsonFileName);

    if ($data && $input) {
        $data = json_decode($data, true);

        if (count($input) !== 1 || !isset ($input["id"]) || !is_numeric((int) ($input["id"]))) {
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


function deleteItem($jsonFileName, $data, $input, $errorStatuses)
{
    $id = $input["id"];
    $foundItem = false;

    if (count($data["items"]) > 1) {
        foreach ($data["items"] as $arr) {
            if ($arr["id"] == $id) {
                $foundItem = true;
                continue;
            } else {
                $newArray[] = $arr;
            }
        }

        if ($foundItem) {
            $newArray = ["items" => $newArray];
            file_put_contents("$jsonFileName", json_encode($newArray));
            showOk();

        } else {
            showError ($errorStatuses[1]);
        }
    }

    else {
        if ($data["items"][0]["id"] === $id) {
            $newArray = ["items" => []];
            file_put_contents("$jsonFileName", json_encode($newArray));
            showOk();
             }

        else {
            showError ($errorStatuses[1]);
        }
    }
}


function showError ($message) {
    echo(json_encode(["error" => $message]));
    exit();
}

function showOk () {
    echo(json_encode(["ok" => true]));
    exit();
}
?>