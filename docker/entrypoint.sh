#!/bin/bash

# Seed the .env file if there is no file present
if [ ! -f "/app/config/.env" ]; then
  cat /podhoard/.env.example > /podhoard/.env
  php artisan key:generate

  mv /podhoard/.env /app/config/.env

  # If this file does not exist, it's likely that the application started for the first time. Allow at least 10 seconds for the database to be ready.
  echo "Waiting for the database to become ready"
  sleep 10

fi

cp /app/config/.env /podhoard/.env
php artisan config:cache
service cron start

# Run PHP preparation commands
php artisan migrate
php artisan db:seed

# Set permissions for logging folder
chmod -R 777 /podhoard/storage

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
