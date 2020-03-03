<?php
define("START_ID", 1);

$input = json_decode(file_get_contents("php://input"), true);
$dataFileName = "todos.json";
//$input = json_decode(file_get_contents("item.json"), true);
$idsFileName = "ids";
$errorStatus = ["Error 500. Internal Server Error", "Error 400. Bad Request"];

checkInfo($dataFileName, $input, $idsFileName, $errorStatus);


function checkInfo($dataFileName, $input, $idsFileName, $errorStatus)
{
     if (!file_exists($idsFileName)) {
        $id = file_put_contents('$idsFileName', START_ID);

    } else {
        $id = file_get_contents($idsFileName);
    }

    if (!file_exists($dataFileName) || empty(file_get_contents($dataFileName))) {
        addFirstItem($input);
        exit();
   }


    if ($dataFileName && $input) {
        $data = json_decode(file_get_contents($dataFileName), true);
        if (count($input) !== 1 || !isset($input["text"]) || !isset($data["items"])) {
            header("HTTP/1.1 $errorStatus[1]");
            die(json_encode(["error" => $errorStatus[1]]));
        }
    }

    addNextItem($dataFileName, $data, $id, $input);
}


function addFirstItem($input) {
    $response[] = ["id" => START_ID, "text" => $input["text"], "checked" => false];
    file_put_contents('ids', START_ID);
    $data[] = json_encode(['items' => $response]);
    file_put_contents("todos.json", $data);
    header("HTTP/1.1 200 OK");
    echo json_encode(["id" => START_ID]);
}


function addNextItem($dataFileName, $data, $id, $input)
{
     $data["items"][] = ["id" => ++$id, "text" => $input["text"], "checked" => false];
     file_put_contents('ids', $id);
    file_put_contents("$dataFileName", json_encode($data));
    header("HTTP/1.1 200 OK");
    echo json_encode(["id" => $id]);
}
?>