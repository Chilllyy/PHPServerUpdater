<?php

use Chilly\Util\Pterodactyl;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

$file = '/tmp/queue.yml';
$yaml = Yaml::parseFile($file);
if (!array_key_exists('id', $yaml)) {
    die("No Queue to run");
}
$upload_id = $yaml['id'];

$pterodactyl = new Pterodactyl();
$pterodactyl->stopServer();