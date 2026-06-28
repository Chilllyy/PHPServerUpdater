#!/bin/bash
set -e

PORT="${SERVER_PORT:-8080}"

cd /home/container/src

echo "Starting queue worker..."
php service.php &
QUEUE_PID=$!

echo "Starting web server on ${PORT}..."
php -S 0.0.0.0:${PORT} -t /home/container/src &
WEB_PID=$!

# If either dies, kill the other
wait -n "$QUEUE_PID" "$WEB_PID"

kill "$QUEUE_PID" "$WEB_PID" 2>/dev/null || true
wait