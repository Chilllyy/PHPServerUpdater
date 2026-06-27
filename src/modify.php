<?php

use Chilly\Util\ServerTemplate;
use Symfony\Component\Yaml\Yaml;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

if (!isset($_GET['id'])) {
    die("No ID provided");
}

$id = $_GET['id'];
$template = ServerTemplate::GetOne($id);

if (isset($_GET['upload'])) {
    echo "TODO Upload to server";
    $yamlString = Yaml::dump(['id' => $id], 2);
    file_put_contents('/tmp/queue.yml', $yamlString);
    $path = realpath(__DIR__ . '/worker.php');
    shell_exec('php ' . $path . ' > /dev/null 2>&1 &');
}

if (isset($_GET['mark'])) {
    $template->markNext();
}

if (isset($_GET['unmark'])) {
    $template->unmarkNext();
}

if (isset($_GET['delete'])) {
    $template->delete();
}

header('location: /');