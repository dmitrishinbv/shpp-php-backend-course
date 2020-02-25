<?php
define("START_ID", 1);

$input = json_decode(file_get_contents("php://input"), true);
$json = 'todos.json';
// $input = 'item.json';
$id = 'ids';
$errors = ["Error 500. Internal Server Error", "Error 400. Bad Request"];

checkInfo($json, $input, $id, $errors);


function checkInfo($json, $input, $id, $errors)
{
    $json = checkJson($json, $errors);
    if (!file_exists($id) && !$json) {
        $id = file_put_contents('ids', START_ID);

    } else if ((!is_readable($id) || !is_writable($id)) && $json) {
        header($errors[0]);
        echo(json_encode(["error" => $errors[0]]));
        exit();

    } else {
        $id = file_get_contents($id);
    }

    if ($json && $input) {
        $json = json_decode($json, true);

        if (!is_array($json) || !is_array($input) || count($input) !== 1 || !array_key_exists("text", $input)
            || !array_key_exists("items", $json)) {
            header($errors[1]);
            echo(json_encode(["error" => $errors[1]]));
            exit();
        }
    }

    addItem($json, $id, $input);
}


function checkJson($json, $errors)
{
    if (is_readable($json) || is_writable($json)) {
        $json = file_get_contents($json);

    } else if (is_readable($json) || !is_writable($json)) {
        header($errors[0]);
        echo(json_encode(["error" => $errors[0]]));
        exit();

    } else {
        $json = false;
    }
    return $json;
}


function addItem($json, $id, $input)
{
    if (!$json) {
        $response[] = array("id" => START_ID, "text" => $input["text"], "checked" => false);
        $id = file_put_contents('ids', START_ID);

    } else {
        $response = $json["items"];
        $response[] = array("id" => ++$id, "text" => $input["text"], "checked" => false);
        file_put_contents('ids', $id);
    }

    $response = array('items' => $response);
    $data[] = json_encode($response);
    file_put_contents("todos.json", $data);
    echo json_encode(["id" => $id]);
}
?>