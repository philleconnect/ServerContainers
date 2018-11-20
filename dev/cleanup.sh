#!/bin/bash
echo "!!! WARNING !!! : This will DELETE ALL CUSTOM FILES (Databases, LDAP, user folders, ...) of this PhilleConnect-Installation!"
read -r -p "Are you sure? [y/N] " response
if [[ "$response" =~ ^([yY][eE][sS]|[yY])+$ ]]
then
    docker-compose stop
    docker rm php_ldap_admin
    docker rm pc_admin
    docker rm samba
    docker rm ldap
    docker rmi servercontainers_php_ldap_admin
    docker rmi servercontainers_pc_admin
    docker rmi servercontainers_samba
    docker rmi servercontainers_ldap
    docker volume rm servercontainers_philleconnect_admin_config
    docker volume rm servercontainers_philleconnect_admin_mysql
    docker volume rm servercontainers_philleconnect_ldap_db
    rm -r mount/samba/students/*/
    rm -r mount/samba/teachers/*/
    rm -r mount/samba/roomExchange/*/
    echo "All docker-stuff has been deleted. You might want to execute 'git clean -f -d' to loose all user data and uncommitted changes as well."
else
    echo "Ok, I did't do anything, lucky you. Be careful!"
fi
