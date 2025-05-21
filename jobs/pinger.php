<?php

require_once __DIR__.'/../vendor/autoload.php';

function response(string $url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $start = microtime(true);
    curl_exec($ch);
    $end = microtime(true);

    curl_close($ch);

    return round(($end - $start) * 1000, 2) . "ms";
}

$url = config("app.url");
printf("%s %s\n", date("Y-m-d H:i:s"), response($url));
