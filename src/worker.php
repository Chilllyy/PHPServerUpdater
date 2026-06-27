<?php

use Chilly\Util\Pterodactyl;
use Symfony\Component\Yaml\Yaml;

include __DIR__ . '/../vendor/autoload.php';
#require_once __DIR__ . '/Util/Init.php';

set_time_limit(0);

$file = '/tmp/queue.yml';
$delete_queue = true;
echo "Loading Queue File\n";
if (!file_exists($file)) {
    echo "No Queue to run\n";
    return;
}
$yaml = Yaml::parseFile($file);
if (!array_key_exists('id', $yaml)) {
    echo "No Queue to run\n";
    return;
}
echo "Loaded Queue!\n";
$upload_id = $yaml['id'];
echo "Found Upload ID: $upload_id\n";
$upload_file = __DIR__ . "/../uploads/$upload_id.zip";
echo "Searching for file $upload_file\n";
if (!file_exists($upload_file)) {
    if ($delete_queue) {
        unlink($file);
    }
    die("File to upload doesn't exist, cancelling");
}
echo "\nCreating Pterodactyl Worker\n";
$pterodactyl = new Pterodactyl();
echo "Created Worker\n";
//BEGIN SERVER ACTIONS
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

$pterodactyl->startServer();

if ($delete_queue) {
    unlink($file);
}