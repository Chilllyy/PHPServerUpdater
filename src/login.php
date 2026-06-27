<?php

use Chilly\Util\Init;

include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    tryLogin();
} else {
    loginPage();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                background-color: #444444;
                color: #E0E0E0;
                margin: 0px;
            }
            body form {
                width: 100%;
                justify-content: center;
                display: flex;
                flex-direction: column;
                padding-top: 100px;
                gap: 10px;
            }
            body form * {
                padding: 10px;
                margin: auto;
            }

            body form p {
                color: red;
                scale: 2;
            }
        </style>
    </head>
    <body>
        <?php
            function tryLogin() {
                if ($_POST['username'] != Init::$config->username) {
                    header('location: /login.php?incorrect=username');
                    exit;
                }
                if (!password_verify(trim($_POST['password']), Init::$config->password)) {
                    header('location: /login.php?incorrect=password');
                    exit;
                }
                session_regenerate_id(true);
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['logged_in'] = true;
                header('location: /');
                exit;
            }
            function loginPage() {
            ?>
            <form action="#" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="text" name="password" placeholder="Password" required>
                <input type="submit" value="submit" name="submit">
                <?php
                if (isset($_GET['incorrect'])) {
                    if ($_GET['incorrect'] == 'password') {
                ?>
                <p>Incorrect Password</p>
                <?php
                    } else if ($_GET['incorrect'] == "username") {
                    ?>
                        <p>Incorrect Username</p>
                    <?php
                    }}
                ?>
            </form>
            <?php
            }
        ?>
    </body>
</html>