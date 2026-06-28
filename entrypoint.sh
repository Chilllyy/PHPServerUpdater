#!/bin/bash

echo "Starting Apache Webserver..."

PORT="${APACHE_PORT:-80}"

echo "Launching Queue Runner..."
php /home/container/src/service.php

sed -i "s/Listen 80/Listen {{SERVER_PORT}}/g" /etc/apache2/ports.conf 
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:{{SERVER_PORT}}>/g" /etc/apache2/sites-available/000-default.conf 
export APACHE_RUN_USER=container 
export APACHE_RUN_GROUP=container 
apache2-foreground && ${STARTUP}