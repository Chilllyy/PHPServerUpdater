<?php

use Chilly\Util\Init;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$config = Init::$config;

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true) {
    header('location: /login.php');
    exit;
}