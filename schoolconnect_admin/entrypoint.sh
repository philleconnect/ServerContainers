#!/bin/bash

#chown -R mysql:mysql /var/lib/mysql
chmod =400 /var/www/html/config/id_rsa

# start services:
service mysql start
service php7.0-fpm start

nginx -g 'daemon off;'
