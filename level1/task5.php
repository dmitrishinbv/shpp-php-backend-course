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

    for ($j = 0; $j < count($headers); $j++) {
        $line = $headers[$j];

        if (preg_match("/Host/", $line[0])) {
            $server = $line[1];
            break;
        }
    }

    $serverStatus = "";
    $server = substr(strstr($server, '.', true), 0);

    if (strnatcmp($server, "student") != 0 && strnatcmp($server, "another") != 0) {
        $server = "else";
    }

    if (strnatcmp($uri, "/") == 0 && strnatcmp($server, "else") != 0) {
        $url = $server . "/index.html";
    }

    else if (strnatcmp($server, "else") == 0) {
        $serverStatus = "404 Not Found";
        $content = "404 page not found";

    } else {
        $url = $server . $uri;
    }

    $inputContent = file_get_contents($url);
    if ($inputContent != null && strnatcmp($serverStatus, "404 Not Found") != 0) {
        $serverStatus = "200 OK";
        $content = $inputContent;
    }

    $body = $content;
    $bodyLength = strlen($body);
    $headersNamesAndValues = "";

    for ($j = 0; $j < count($headers); $j++) {
        $line = $headers[$j];

        if (!preg_match("/Content-Length/", $line[0])) {
            if ($line[0] != "") {
                $headersNamesAndValues .= $line[0];
                $headersNamesAndValues .= ": ";
            }
            if ($line[1] != "") {
                $headersNamesAndValues .= $line[1];
                $headersNamesAndValues .= "\n";
            }

        } else {
            $headersNamesAndValues .= "Content-Length: $bodyLength";
        }
    }

    echo
"HTTP/1.1 $serverStatus
$headersNamesAndValues
$body";

//    $response = $method." ";
//    $response .= $uri;
//
//    Header ("Location:index.php?get=$response");
//    Header ("Location:index.php?body=$body");
}

$parseRequest = parseTcpStringAsHttpRequest($contents);
$method = $parseRequest["method"];
$uri = $parseRequest["uri"];
$headers = $parseRequest["headers"];
$body = $parseRequest["body"];

$http = processHttpRequest($method, $uri, $headers, $body);
?>
