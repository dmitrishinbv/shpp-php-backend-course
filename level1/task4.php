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
    $contentType = "";

    for ($i = 0; $i < count($headers); $i++) {
        $line = implode($headers[$i]);
        if (preg_match("/Content-Type/", $line)) {
            $contentType = implode(": ", $headers[$i]);
            $contentType = substr(strstr($contentType, ': '), 2);
            break;
        }
    }

    if (!preg_match("/POST/", $method) || !preg_match("/api\\/checkLoginAndPassword/", $uri)
        || !preg_match("/application\\/x\\-www\\-form\\-urlencoded/", $contentType)) {
        $serverStatus = "404 Not Found";
        $content = "404 page not found";

    } else {
        $userLogin = substr(strstr($body, '&', true), 0);
        $userLogin = substr($userLogin, strripos($userLogin, '=') + 1);
        $userPassword = substr($body, strripos($body, "=") + 1);
        $inputFile = file_get_contents("passwords.txt");
        $currentLogin = "";
        $currentPassword = "";

        if ($inputFile != null) {
            $serverStatus = "200 OK";
            $passwordList = preg_split('/\r\n|\r|\n/', $inputFile);
            foreach ($passwordList as $entry) {
                $currentLogin = substr(strstr($entry, ':', true), 0);
                if (strnatcmp($userLogin, $currentLogin) == 0) {
                    $currentPassword = substr($entry, strripos($entry, ":") + 1);
                    break;
                }

            }
            if ($currentLogin == "" || strnatcmp($currentPassword, $userPassword) != 0) {
                $content = "NOT FOUND";
            } else {
                $content = "FOUND";
            }
        } else {
            $serverStatus = "500 Internal Server Error";
            $content = "internal server error";
        }
    }

    $body = "<h1 style='color: green'>$content</h1>";
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
"$method $uri HTTP/1.1 $serverStatus
$headersNamesAndValues

$body";
}

$parseRequest = parseTcpStringAsHttpRequest($contents);
$method = $parseRequest["method"];
$uri = $parseRequest["uri"];
$headers = $parseRequest["headers"];
$body = $parseRequest["body"];

$http = processHttpRequest($method, $uri, $headers, $body);
?>
