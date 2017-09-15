#!/bin/bash

#chown -R mysql:mysql /var/lib/mysql

# start services:
service mysql start
service php7.0-fpm start

nginx -g 'daemon off;'
