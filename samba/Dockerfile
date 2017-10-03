FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

#RUN apt-get update
# apt-get update &&
RUN apt-get update && apt-get install -y --no-install-recommends expect nano nmap vim
# TODO: rauswerfen wenn fertig
RUN apt-get update && apt-get install -y --no-install-recommends libnss-ldap libpam-ldap samba smbldap-tools
# dpkg-dev

#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# When not limiting the open file descritors limit, the memory consumption of
# slapd is absurdly high. See https://github.com/docker/docker/issues/8231
RUN ulimit -n 8192

COPY debconf_libnss-ldap /root/
COPY smbldap.conf /etc/smbldap-tools/
COPY smbldap_bind.conf /etc/smbldap-tools/
COPY smbconfadd /root/
COPY smbFolders /root/
# TODO: for some reason debconf won't set this file right, so we need to copy it to place in entrypoint:
COPY libnss-ldap.conf /root/
COPY entrypoint.sh /entrypoint.sh

RUN echo "session optional        pam_mkhomedir.so skel=/etc/skel umask=077" >> /etc/pam.d/common-session
RUN cp -f /etc/samba/smb.conf /root/smb.conf.tpl

EXPOSE 137
EXPOSE 138
EXPOSE 139

CMD ["./entrypoint.sh"]
