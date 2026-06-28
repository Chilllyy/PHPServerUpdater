#!/bin/bash

echo "Starting Apache Webserver..."

PORT="${APACHE_PORT:-80}"

sed -i "s/^Listen 80/c\Listen $PORT" /usr/local/apache2/conf/httpd.conf

apache2-foreground &

echo "Waiting For Apache..."
sleep 2

echo "Launching Queue Runner..."
php /home/container/src/service.php