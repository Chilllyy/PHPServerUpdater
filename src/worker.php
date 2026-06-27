<?php

use Chilly\Util\Pterodactyl;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

$file = '/tmp/queue.yml';
echo "Loading Queue File\n";
$yaml = Yaml::parseFile($file);
if (!array_key_exists('id', $yaml)) {
    die("No Queue to run");
}
echo "Loaded Queue!\n";
$upload_id = $yaml['id'];
echo "Found Upload ID: " , $upload_id;
echo "\nCreating Pterodactyl Worker";
$pterodactyl = new Pterodactyl();
echo "Created Worker\n";
$pterodactyl->stopServer();
echo "Stopping Server\n";