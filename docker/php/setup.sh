#!/bin/bash
echo "185.199.108.133 raw.githubusercontent.com" >> /etc/hosts
cd /var/www/demo
php composer install
php bin/console d:m:migrate
php bin/console lexik:jwt:generate-keypair --skip-if-exists
php-fpm