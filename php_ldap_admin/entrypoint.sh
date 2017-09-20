#!/bin/bash

# check if environment variables are set with -e option:
if [[ -z "$SLAPD_PASSWORD" ]]; then
        echo -n >&2 "Error: Container not configured and SLAPD_PASSWORD not set. "
        echo >&2 "Did you forget to add -e SLAPD_PASSWORD=... ?"
        exit 1
fi
if [[ -z "$SLAPD_DOMAIN0" ]]; then
        echo -n"SLAPD_DOMAIN0 not set."
        echo -n"I am using 'local'"
        SLAPD_DOMAIN0='local'
fi
if [[ -z "$SLAPD_DOMAIN1" ]]; then
        echo -n"SLAPD_DOMAIN1 not set."
        echo -n"I am using 'slapd'"
        SLAPD_DOMAIN1='slapd'
fi

# set the environment settings for slapd:
sed -i "s|$servers->setValue('server','host','127.0.0.1');|$servers->setValue('server','host','$SLAPD_DOMAIN1.$SLAPD_DOMAIN0');|g" /etc/phpldapadmin/config.php
sed -i "s|$servers->setValue('server','base',array('dc=example,dc=com'));|$servers->setValue('server','base',array('dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0'));|g" /etc/phpldapadmin/config.php
sed -i "s|$servers->setValue('login','bind_id','cn=admin,dc=example,dc=com');|$servers->setValue('login','bind_id','cn=admin,dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0');|g" /etc/phpldapadmin/config.php
sed -i "s|// $config->custom->appearance['hide_template_warning'] = false;|$config->custom->appearance['hide_template_warning'] = true;|g" /etc/phpldapadmin/config.php

# check connection, TODO: raus werfen!
#ldapsearch -x -h slapd -b "cn=admin,dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0"

#chown -R openldap:openldap /var/lib/ldap/ /var/run/slapd/
/usr/sbin/apache2ctl -D FOREGROUND
