#!/bin/bash
wget --post-data "url=localhost&user=sql_user&password=sql_password&name=sql_database&isReady=true" http://localhost/setup/database.php
wget --post-data "url=ldap&password=ldap_password&basedn=ldap_basedn&admindn=cn=admin&usersdn=ou=users&groupsdn=ou=groups&studentscn=cn=students&teacherscn=cn=teachers&hostname=PHILLECONNECT&isReady=true" http://localhost/setup/ldap.php
