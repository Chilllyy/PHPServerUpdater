#!/bin/bash

echo "Starting Apache Webserver..."

apache2-forground &

echo "Waiting For Apache..."
sleep 2

echo "Launching Queue Runner..."
php /home/container/src/service.php