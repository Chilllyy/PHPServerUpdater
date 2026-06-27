<?php

use Chilly\Util\Init;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
session_start();
$config = Init::$config;

if (isset($_SESSION['username'])) {
    if ($_SESSION['username'] != $config->username) {
        session_destroy();
        session_unset();
        die("Not Authenticated!");
    }
    if (isset($_SESSION['password'])) {
        if ($_SESSION['password'] != $config->password) {
            session_destroy();
            session_unset();
            die("Not Authenticated!");
        }
    }
}