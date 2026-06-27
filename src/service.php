<?php
include __DIR__ . '/../vendor/autoload.php';

while (true) {
    print("Checking Queue Worker!");
    $path = realpath(__DIR__ . '/worker.php');
    shell_exec("php $path");
    sleep(5);
}