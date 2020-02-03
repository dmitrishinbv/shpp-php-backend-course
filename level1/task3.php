<?php

function readHttpLikeInput()
{
    $f = fopen('php://stdin', 'r');
    $store = "";
    $toread = 0;
    while ($line = fgets($f)) {
        $store .= preg_replace("/\r/", "", $line);
        if (preg_match('/Content-Length: (\d+)/', $line, $m))
            $toread = $m[1] * 1;
        if ($line == "\r\n")
            break;
    }
    if ($toread > 0)
        $store .= fread($f, $toread);
    return $store;
}

$contents = readHttpLikeInput();


function parseTcpStringAsHttpRequest($string)
{
    $emptyHeaders = false;
    $emptyBody = false;
    $list = preg_split('/\r\n|\r|\n/', $string);
    $headersNamesAndValues = array();

    if (count($list) == 1 || !empty($list[count($list)-2])) {
        $emptyBody = true;
    }

    for ($i = 1; $i < count($list); $i++) {
        array_push($headersNamesAndValues, explode(": ", $list[$i]));

        if (empty($list[$i])) {
            array_pop($headersNamesAndValues);
            break;
        }
    }

    if (empty($headersNamesAndValues)) {
        $emptyHeaders = true;
    }

    $httpRequestPieces = explode(" ", $string);

    if (!$emptyBody && !$emptyHeaders) {
        return array(
            "method" => $httpRequestPieces[0],
            "uri" => $httpRequestPieces[1],
            "headers" => $headersNamesAndValues,
            "body" => $list[count($list) - 1],
        );
    }

    if ($emptyBody && !$emptyHeaders) {
        return array(
            "method" => $httpRequestPieces[0],
            "uri" => $httpRequestPieces[1],
            "headers" => $headersNamesAndValues,
            "body" => null,
        );
    }

    if (!$emptyBody && $emptyHeaders) {
        return array(
            "method" => $httpRequestPieces[0],
            "uri" => $httpRequestPieces[1],
            "headers" => null,
            "body" => $list[count($list) - 1],
        );
    }

    else {
        return array(
            "method" => $httpRequestPieces[0],
            "uri" => $httpRequestPieces[1],
            "headers" => null,
            "body" => null,
        );
    }
}


function processHttpRequest($method, $uri, $headers, $body)
{
    $date = "Date: " . date("D, d M Y H:i:s", time()) . " GMT";
    $parts = substr(strstr($uri, '='), 0, strlen($uri));
    $nums = preg_replace("/[^0-9]/", "", $parts);

    $content = 0;
    for ($i = 0, $j = strlen($nums); $i < $j; $i++) {
        $value = $nums[$i] + 0;
        $content += $value;
    }

    $contentLength = strlen($content . "");
    $serverStatus = "";

    if (preg_match("/GET/", $method) && preg_match("/sum/", $uri)) {
        $serverStatus = "200 OK";

    }
    if (preg_match("/GET/", $method) && !preg_match("/sum/", $uri)) {
        $serverStatus = "404 Not Found";
        $content = "not found";
    }

    if (!preg_match("/GET/", $method) && !preg_match("/nums/", $uri)) {
        $serverStatus = "400 Bad Request";
        $content = "bad request";
    }


    echo
"HTTP/1.1 $serverStatus
Server: Apache/2.2.14 (Win32)
Connection: Closed
Content-Type: text/html; charset=utf-8
Content-Length: $contentLength

$content";
}

$parseRequest = parseTcpStringAsHttpRequest($contents);

$method = $parseRequest["method"];
$uri = $parseRequest["uri"];
$headers = $parseRequest["headers"];
$body = $parseRequest["body"];

$http = processHttpRequest($method, $uri, $headers, $body);
?>
