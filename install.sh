#!/bin/bash

if [ -d vendor ]
then
    rm -Rf vendor
fi

echo "Doing composer install..."
composer install

echo "Initializing the migration..."
if [ -f database/database.sqlite ]
then
    rm database/database.sqlite
fi
touch database/database.sqlite
php artisan migrate --seed

echo "Starting the php server"
php -S localhost:8000 -t public/
