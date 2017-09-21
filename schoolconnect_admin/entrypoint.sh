#!/bin/bash

#chown -R mysql:mysql /var/lib/mysql

# start services:
service mysql start
service php7.0-fpm start
#echo "create database databasename" | mysql -u root --password=password
#mysql -u root --password=password databasename < /var/www/html/Rechnerverwaltung/philleconnect-structure.sql
#rm /var/www/html/Rechnerverwaltung/philleconnect-structure.sql

# init database
echo "create database sql_database;" | mysql -u root --password=$MYSQL_PASSWORD
echo "CREATE USER 'sql_user'@'localhost' IDENTIFIED BY '$MYSQL_PASSWORD';" | mysql -u root --password=$MYSQL_PASSWORD
echo "grant all privileges on sql_database.* to 'sql_user'@'localhost';" | mysql -u root --password=$MYSQL_PASSWORD

sed -i "s|sql_password|$MYSQL_PASSWORD|g" /var/www/html/setup/database.php
sed -i "s|ldap_url|ldap|g" /var/www/html/setup/ldap.php
sed -i "s|ldap_password|$SLAPD_PASSWORD|g" /var/www/html/setup/ldap.php
sed -i "s|ldap_basedn|dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0|g" /var/www/html/setup/ldap.php
sed -i "s|ldap_admindn|cn=admin|g" /var/www/html/setup/ldap.php
sed -i "s|ldap_hostname|ldap|g" /var/www/html/setup/ldap.php

#openssl genrsa -out /etc/nginx/privkey.pem 2048
#openssl req -new -x509 -key /etc/nginx/privkey.pem -out /etc/nginx/cacert.pem -days 36500
# TODO: generate keys on build as soon as we are going stable

# generate new rsa-key to connect to ipfire via ssh (only if not exists):
if [ ! -f /var/www/html/config/id_rsa ]; then
    ssh-keygen -t rsa -N "" -f /var/www/html/config/id_rsa
fi
chmod =400 /var/www/html/config/id_rsa
mv -f /htaccess /var/www/html/config/.htaccess

sed -i "s|root|philleconnect|g" /var/www/html/config/id_rsa.pub
# TODO: make the pub key downloadable from GUI

#==============================================

nginx -g 'daemon off;'
