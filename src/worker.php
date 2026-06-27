<?php

use Chilly\Util\Pterodactyl;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

set_time_limit(0);

$file = '/tmp/queue.yml';
echo "Loading Queue File\n";
$yaml = Yaml::parseFile($file);
if (!array_key_exists('id', $yaml)) {
    die("No Queue to run");
}
echo "Loaded Queue!\n";
$upload_id = $yaml['id'];
echo "Found Upload ID: $upload_id";
echo "\nCreating Pterodactyl Worker\n";
$pterodactyl = new Pterodactyl();
echo "Created Worker\n";
$pterodactyl->stopServer();
echo "Stopping Server\n";
$stopped = false;
while (!$stopped) {
    $stopped = !$pterodactyl->getServerRunning();
    echo "Server Still running, waiting 5 seconds";
    sleep(5);
}
echo "Server Stopped! creating backup now";
$backup_uuid = $pterodactyl->createBackup();
echo "Created Backup with UUID $backup_uuid";