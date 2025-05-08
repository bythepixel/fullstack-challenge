#!/bin/bash

cd /var/www/html
cp .env.example .env
chmod 777 .env
composer install
php artisan key:generate
php artisan migrate:fresh --seed

chmod -R 777 storage
chmod -R 777 bootstrap

php artisan cache-weather
php artisan cache-weather --extended
