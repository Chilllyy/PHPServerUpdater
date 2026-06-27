<?php

use Chilly\Util\Pterodactyl;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

set_time_limit(0);

$file = '/tmp/queue.yml';
$delete_queue = false;
echo "Loading Queue File\n";
$yaml = Yaml::parseFile($file);
if (!array_key_exists('id', $yaml)) {
    die("No Queue to run");
}
echo "Loaded Queue!\n";
$upload_id = $yaml['id'];
echo "Found Upload ID: $upload_id";
$upload_file = __DIR__ . "/../uploads/$upload_id.zip";
if (!file_exists($upload_file)) {
    if ($delete_queue) {
        unlink($file);
    }
    die("File to upload doesn't exist, cancelling");
}
echo "\nCreating Pterodactyl Worker\n";
$pterodactyl = new Pterodactyl();
echo "Created Worker\n";
$pterodactyl->stopServer();
echo "Stopping Server\n";
sleep(10); //Artificial pause to help with rate limiting
$stopped = !$pterodactyl->getServerRunning();
while (!$stopped) {
    echo "Server Still running, waiting 5 seconds\n";
    $stopped = !$pterodactyl->getServerRunning();
    sleep(5);
}
echo "Server Stopped! creating backup now\n";
$backup_uuid = $pterodactyl->createBackup();
echo "Creating Backup with UUID $backup_uuid\n";
sleep(10); //Artificial Pause to help with rate limiting
$backup_finished = $pterodactyl->checkBackup($backup_uuid);
while (!$backup_finished) {
    echo "Server Still backing up, waiting 5 seconds\n";
    $backup_finished = $pterodactyl->checkBackup($backup_uuid);
    sleep(5);
}
$files = [
    'world'
];
//Delete Plugins folder if there is a backup under uploads, else just leave it as is
if (file_exists(__DIR__ . '/../uploads/plugins.zip')) {
    $files[] = 'plugins';
}

$pterodactyl->deleteFile($files);

$pterodactyl->uploadFile($upload_file, "template.zip");



if ($delete_queue) {
    unlink($file);
}