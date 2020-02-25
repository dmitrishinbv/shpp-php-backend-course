<?php
$input = json_decode(file_get_contents("php://input"), true);

$json = 'todos.json';
//$input = json_decode(file_get_contents('item.json'), true);
$errors = ["Error 500. \"Internal Server Error\"", "Error 400. \"Bad Request\""];

checkInfo($json, $input, $errors);

function checkInfo($json, $input, $errors)
{
    $json = checkJson($json, $errors);

    if ($json) {
        $json = json_decode($json, true);

        if (!is_array($json) || !is_array($input) || count($input) != 1 || !array_key_exists("id", $input)) {
            echo(json_encode(["error" => $errors[1]]));
            exit();
        }

        deleteItem($json, $input, $errors);

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


function deleteItem($json, $input, $errors)
{
    $itemsArray = $json["items"];
    $id = $input["id"];
    $foundItem = false;

    if (count($itemsArray) > 1) {
        foreach ($itemsArray as $arr) {
            if ($arr["id"] === $id) {
                $foundItem = true;
                continue;
            } else {
                $newArray[] = $arr;
            }
        }

        if ($foundItem) {
            $newArray = array("items" => $newArray);
            file_put_contents("todos.json", json_encode($newArray));
            showOk();

        } else {
            showError ($errors[1]);
        }
    }

    else {
        if ($itemsArray[0]["id"] === $id) {
            $newArray = array("items" => []);
            file_put_contents("todos.json", json_encode($newArray));
            showOk();
             }

        else {
            showError ($errors[1]);
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