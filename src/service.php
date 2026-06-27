<?php
include __DIR__ . '/../vendor/autoload.php';

set_time_limit(0);

while (true) {
    print("Checking Queue Worker!");
    $path = realpath(__DIR__ . '/worker.php');
    require __DIR__ . '/worker.php';
    shell_exec("php $path");
    sleep(5);
}