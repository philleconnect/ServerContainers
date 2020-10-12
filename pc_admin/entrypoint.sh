#!/bin/bash

# Function for a clean shutdown of the container
function shutdown {
    kill -TERM "$NGINX_PROCESS" 2>/dev/null
    exit
}
trap shutdown SIGTERM

chmod 0777 /home
chmod 0777 /home/*

# prepare setup as far as possible and run it if config is still empty:
if [ ! -f "/etc/pc_admin/.DatabaseSetupDone" ]
then
    while ! mysqladmin ping -h"main_db" --silent; do
        sleep 1
    done
    mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h main_db schoolconnect < db.sql
    mysql -u $MYSQL_USER -p$MYSQL_PASSWORD -h main_db schoolconnect < content_initial.sql
    touch /etc/pc_admin/.DatabaseSetupDone
fi

# Wait for ldap container to be up and running
while ! ping -c 1 -n -w 1 ldap &> /dev/null; do
    sleep 1
done

# Wait for samba container to be up and running
while ! ping -c 1 -n -w 1 samba &> /dev/null; do
    sleep 1
done

# Wait for ldap server to be accessible
function testLdap {
    (ldapsearch -x -LLL -h ldap -D "cn=admin" -w $SLAPD_PASSWORD -b"dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0" -s sub "(objectClass=user)" givenName &> /dev/null)
    if [ $? -gt 254 ]; then
        echo -1
    else
        echo 0
    fi
}
while [ $(testLdap) -eq -1 ]; do
    sleep 1
done

# Write the servermanager API key to a permanent file
echo $APIKEY > /etc/pc_admin/apikey.txt

# Write the shared secret of the management apis to a permanent file
echo $MANAGEMENT_APIS_SHARED_SECRET > /etc/pc_admin/managementsecret.txt

# generate https key and certificate
if [ ! -f /etc/pc_admin/privkey.pem ]; then
    openssl genrsa -out /etc/pc_admin/privkey.pem 2048
    openssl req -new -x509 -key /etc/pc_admin/privkey.pem -out /etc/pc_admin/cacert.pem -days 36500 -subj "/C=DE/ST=Germany/L=Germany/O=SchoolConnect/OU=Schulserver/CN=example.com"
fi
cp /etc/pc_admin/privkey.pem /etc/nginx/privkey.pem
cp /etc/pc_admin/cacert.pem /etc/nginx/cacert.pem

# generate new rsa-key to connect to ipfire via ssh (only if not exists):
if [ ! -f /etc/pc_admin/id_rsa ]; then
    ssh-keygen -t rsa -N "" -f /etc/pc_admin/id_rsa
fi
chmod =400 /etc/pc_admin/id_rsa

sed -i "s|root|schoolconnect|g" /etc/pc_admin/id_rsa.pub
# TODO: make the pub key downloadable from GUI

#==============================================

echo "waiting for database..."
while ! mysqladmin ping -h"main_db" --silent; do
    sleep 1
done
echo 'everything is prepared, starting server for pc_admin'
python3 /usr/local/bin/api/integritycheckProcess.py &
cd /usr/local/bin/api
uwsgi --ini api.ini --lazy &
cd
nginx -g 'daemon off;' &
NGINX_PROCESS=$!
wait $NGINX_PROCESS
