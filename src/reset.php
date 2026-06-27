<?php

use Chilly\Util\Init;
use Chilly\Util\ServerTemplate;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
function getBearer() {
    $headers = $_SERVER['Authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;

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
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

if (!isset($_POST['id'])) {
    http_response_code(400);
    exit("Missing ID");
}

$id = $_POST['id'];
if (ServerTemplate::GetOne($id)) {
    http_response_code(400);
    exit("Invalid ID");
}

$yamlString = Yaml::dump(['id' => $id], 2);
file_put_contents('/tmp/queue.yml', $yamlString);
