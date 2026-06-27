<?php

use Chilly\Util\ServerTemplate;
require_once __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Util/Init.php';

if (!isset($_POST['template_name'])) {
    die("Invalid, please navigate to the webroot");
}

$file = $_FILES['template_file'];
$name = $_POST['template_name'];


print("File: " . $file['name']);
?>
<br>
<?php
print("Template Name: " . $name);
?>
<br>
<?php
$template = ServerTemplate::Create($name);
if (!$template) {
    die("Unable to create server template, please check DB");
}

$target_file = __DIR__ . '/../uploads/' . $template->id . ".zip";
if (file_exists($target_file)) {
    die("File Already exists: " . $target_file);
}

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    echo "File Uploaded!";
    header("location: /");
    return;
}
die("There was an error uploading the file");