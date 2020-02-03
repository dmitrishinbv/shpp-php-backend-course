<?php

// не обращайте на эту функцию внимания
// она нужна для того чтобы правильно считать входные данные
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

$http = parseTcpStringAsHttpRequest($contents);
echo(json_encode($http, JSON_PRETTY_PRINT));
?>