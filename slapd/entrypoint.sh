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

# Start Samba-Server:
# -------------------
service samba start

# ------------------------------------
# Install the slapd (openLDAP) server:
# ------------------------------------

# set the environment variables for slapd:
sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /root/debconf_slapd
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /root/debconf_slapd
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /root/debconf_slapd
sed -i "s|SLAPD_ORGANIZATION|$SLAPD_ORGANIZATION|g" /root/debconf_slapd
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /root/add_user.ldif
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /root/add_user.ldif
debconf-set-selections /root/debconf_slapd

#dpkg-reconfigure -f noninteractive slapd >/dev/null 2>&1
dpkg-reconfigure slapd

chown -R openldap:openldap /var/lib/ldap/ /var/run/slapd/


# ---------------------
# configure libnss-ldap
# ---------------------

sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /root/debconf_libnss-ldap
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /root/debconf_libnss-ldap
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /root/debconf_libnss-ldap
debconf-set-selections /root/debconf_libnss-ldap
dpkg-reconfigure libnss-ldap
#apt-get install libnss-ldap

# ------------------------------
# setup the connection to samba:
# ------------------------------

# TODO: Besser machen: nur Anfang der Zeile mit sed parsen, dann ganze Zeile ersetzen
sed -i "s|passwd:         compat|passwd:         compat ldap|g" /etc/nsswitch.conf
sed -i "s|group:          compat|group:          compat ldap|g" /etc/nsswitch.conf
sed -i "s|shadow:         compat|shadow:         compat ldap|g" /etc/nsswitch.conf

sed -i "s/password        [success=1 user_unknown=ignore default=die]     pam_ldap.so use_authtok try_first_pass/password        [success=1 user_unknown=ignore default=die]     pam_ldap.so try_first_pass/g" /etc/pam.d/common-password
echo "session optional        pam_mkhomedir.so skel=/etc/skel umask=077" >> /etc/pam.d/common-session

service slapd start
ldapadd -x -D cn=admin,dc=$SLAPD_DOMAIN1,dc=$SLAPD_DOMAIN0 -w $SLAPD_PASSWORD -f /root/add_user.ldif
service slapd stop
killall slapd
#slapadd -F /etc/ldap/slapd.d -l /root/add_user.ldif

# installation of samba-schema:
# -----------------------------
#cp /usr/share/doc/samba/examples/LDAP/samba.schema.gz /etc/ldap/schema
cp /root/samba.schema.gz /etc/ldap/schema
gzip -d /etc/ldap/schema/samba.schema.gz
mkdir /tmp/ldif_output
slapcat -f /root/schema_convert.conf -F /tmp/ldif_output -n 0 | grep samba,cn=schema
slapcat -f /root/schema_convert.conf -F /tmp/ldif_output -n0 -H ldap:///cn={14}samba,cn=schema,cn=config -l cn=samba.ldif
service slapd start # TODO: Is there a better way?
ldapmodify -Q -Y EXTERNAL -H ldapi:/// -f /root/samba_indices.ldif
service slapd stop
killall slapd

# set configurations for smbldap (which is done by smbldap-conf on a running system):
# -----------------------------------------------------------------------------------
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /etc/smbldap-tools/smbldap.conf
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /etc/smbldap-tools/smbldap.conf
sed -i "s|SLAPD_DOMAIN0|$SLAPD_DOMAIN0|g" /etc/smbldap-tools/smbldap_bind.conf
sed -i "s|SLAPD_DOMAIN1|$SLAPD_DOMAIN1|g" /etc/smbldap-tools/smbldap_bind.conf
sed -i "s|SLAPD_PASSWORD|$SLAPD_PASSWORD|g" /etc/smbldap-tools/smbldap_bind.conf
service slapd start # TODO: Is there a better way?
smbldap-populate $SLAPD_PASSWORD -u 10000 -g 10000
service slapd stop
killall slapd

sed -i "s/SLAPD_DOMAIN0/$SLAPD_DOMAIN0/g" /root/smbconfadd
sed -i "s/SLAPD_DOMAIN1/$SLAPD_DOMAIN1/g" /root/smbconfadd
sed -i '/\[global\]/a security = user' /etc/samba/smb.conf
sed -i 's/.*passdb backend =.*/# EDITED: ldap connection setup for samba:/g' /etc/samba/smb.conf
sed -i '/# EDITED: ldap connection setup for samba:/ r /root/smbconfadd' /etc/samba/smb.conf

cat /root/smbFolders >> /etc/samba/smb.conf

smbpasswd -w $SLAPD_PASSWORD

# Getting it up and insert adminuser:
# -----------------------------------
service slapd start # TODO: Is there a better way?
#smbldap-useradd -a adminuser
#smbldap-passwd $SLAPD_PASSWORD
#add necessary groups:
smbldap-groupadd -a teachers
smbldap-groupadd -a students
#add users to groups:
#smbldap-groupmod -m "username,username" "groupname"
#remove users from groups
#smbldap-groupmod -x "username,username" "groupname"
service slapd stop
killall slapd

#Optionally make sure homedirs are created on login: (TODO: Does this work?)
# echo -e 'session required\t\t\tpam_mkhomedir.so' >> /etc/pam.d/common-session

# Start slapd in foreground:
# --------------------------
slapd -d 32768
#exec "$@"
