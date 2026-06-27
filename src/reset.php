<?php

use Chilly\Util\Init;
use Chilly\Util\ServerTemplate;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
function getBearer() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }

    if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

$token = getBearer();
if (!$token || $token != Init::$config->key) {
    http_response_code(401);
    exit("Unauthorized");
}

//Authorized
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    exit("Method Not Allowed");
}

$template = ServerTemplate::GetRandom();
$id = $template->id;

$yamlString = Yaml::dump(['id' => $id], 2);
file_put_contents('/tmp/queue.yml', $yamlString);