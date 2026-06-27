<?php

use Chilly\Util\ServerTemplate;
include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Util/Init.php';
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
            .grid-container {
                width: 100%;
                padding-top: 50px;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
            }
            .grid-item {
                width: 25%;
                background-color: rgba(32,32,32,0.8);
                border: 2px solid rgba(24,24,24,0.8);
                border-radius: 25px;
                padding: 20px;
                font-size: 30px;
                text-align: center;
            }

            .form-container {
                background-color: #121212;
                padding: 4vh;
            }

            .form-container form {
                display: flex;
                flex-direction: column;
            }

            .form-container form input
            {
                margin: 10px;
            }

            .form-container form input[type=file]
            {
                width: 20%;
            }
            
            .form-container form input:hover
            {
                cursor: pointer;
            }

            .form-container form input[type=text]
            {
                width: 25%;
            }
            .form-container form input[type=text]:hover
            {
                cursor: text;
            }

            .form-container form input[type=submit]
            {
                width: 10%;
            }

            .grid-item.next
            {
                border-color: gold;
            }

            body {
                background-color: #444444;
                color: #E0E0E0;
                margin: 0px;
            }
        </style>
    </head>
    <body>
        <div class="form-container">
            <p>Upload New Template</p>
            <form class="input-form" action="upload.php" method="post" enctype="multipart/form-data">
                <input type="text" name="template_name" id="template_name" placeholder="Template Name" required>
                <input type="file" name="template_file" id="template_file" accept="application/zip" required>
                <input type="submit" value="Upload">
            </form>
        </div>
        <div class="grid-container">
            <?php
            $templates = ServerTemplate::GetAll();
            foreach($templates as $template) {
            ?>
            <div class="grid-item <?php echo $template->is_next ? "next" : ""; ?>">
                <p><?php echo $template->template_name ?></p>
                <form action="modify.php" method="get">
                    <input type="hidden" name="id" value="<?php echo $template->id; ?>">
                    <input type="submit" value="Upload" name="upload">
                    <input type="submit" value="<?php echo $template->is_next ? "Unmark as next" : "Mark as next"; ?>" name="<?php echo $template->is_next ? "unmark" : "mark"; ?>">
                    <input type="submit" value="Delete" name="delete">
                </form>
            </div>
            <?php }?>
        </div>
    </body>
</html>