<?php
include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';

set_time_limit(0);

while (true) {
    print("Checking Queue Worker!\n\n");
    $path = realpath(__DIR__ . '/worker.php');
    require __DIR__ . '/worker.php';
    shell_exec("php $path");
    sleep(5);
}