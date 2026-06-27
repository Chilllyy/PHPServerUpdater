<?php
include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
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
        <link rel="stylesheet" href="">
    </head>
    <body>
        <?php
            function tryLogin() {
            
            }
            function loginPage() {
            ?>
            <?php
            }
        ?>
    </body>
</html>