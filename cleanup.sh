#!/bin/bash
docker rm pc_admin
docker rm samba
docker rm ldap
docker rm php_ldap_admin
docker rmi philleconnect_ldap
docker rmi philleconnect_pc_admin
docker rmi philleconnect_samba
docker rmi philleconnect_php_ldap_admin
docker volume rm philleconnect_philleconnect_admin_config
docker volume rm philleconnect_philleconnect_ldap_db
docker volume rm philleconnect_philleconnect_admin_mysql
