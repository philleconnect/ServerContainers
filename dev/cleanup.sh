#!/bin/bash
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
