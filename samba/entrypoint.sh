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
if [[ -z "$SLAPD_ORGANIZATION" ]]; then
        echo -n"SLAPD_ORGANIZATION not set."
        echo -n"I am using 'My School'"
        SLAPD_ORGANIZATION='My School'
fi

# ---------------------
# configure libnss-ldap
# ---------------------

echo "configuring slapd and libnss-ldap..."
sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /root/debconf_libnss-ldap
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /root/debconf_libnss-ldap
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /root/debconf_libnss-ldap
debconf-set-selections /root/debconf_libnss-ldap
dpkg-reconfigure libnss-ldap
#apt-get install libnss-ldap

sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /etc/smbldap-tools/smbldap_bind.conf
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /etc/smbldap-tools/smbldap_bind.conf 
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /etc/smbldap-tools/smbldap_bind.conf

sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /etc/smbldap-tools/smbldap.conf
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /etc/smbldap-tools/smbldap.conf 
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /etc/smbldap-tools/smbldap.conf

# -------------------------------
# setup the connection to sldapd:
# -------------------------------

echo "setting up connection to slapd..."
# TODO: Besser machen: nur Anfang der Zeile mit sed parsen, dann ganze Zeile ersetzen
sed -i "s|passwd:         compat|passwd:         compat ldap|g" /etc/nsswitch.conf
sed -i "s|group:          compat|group:          compat ldap|g" /etc/nsswitch.conf
sed -i "s|shadow:         compat|shadow:         compat ldap|g" /etc/nsswitch.conf

sed -i "s/password\t\[success=1 user_unknown=ignore default=die\]\tpam_ldap\.so use_authtok try_first_pass/password\t\[success=1 user_unknown=ignore default=die\]\tpam_ldap\.so try_first_pass/g" /etc/pam.d/common-password

echo "configuring smb.conf..."
sed -i "s/SLAPD_DOMAIN0/$SLAPD_DOMAIN0/g" /root/smbconfadd
sed -i "s/SLAPD_DOMAIN1/$SLAPD_DOMAIN1/g" /root/smbconfadd
sed -i '/\[global\]/a security = user' /etc/samba/smb.conf
sed -i 's/.*passdb backend =.*/# EDITED: ldap connection setup for samba:/g' /etc/samba/smb.conf
sed -i '/# EDITED: ldap connection setup for samba:/ r /root/smbconfadd' /etc/samba/smb.conf

smbpasswd -w $SLAPD_PASSWORD

# Getting it up and insert adminuser:
# -----------------------------------
echo "adding groups to samba..."
service nmbd start
service smbd start
smbldap-groupadd -a teachers
smbldap-groupadd -a students
#service smbd stop
#add users to groups:
#smbldap-useradd -a testuser

#=======================================================

echo "configuration finished, starting now..."
#service nmbd start
#smbd -i
while true; do sleep 1; done # hack to keep the docker running...
