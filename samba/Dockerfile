FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

#RUN apt-get update
# apt-get update &&
#RUN apt-get update && apt-get install -y --no-install-recommends expect nano nmap procps
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
COPY build.sh /build.sh
COPY entrypoint.sh /entrypoint.sh

RUN ./build.sh

EXPOSE 137
EXPOSE 138
EXPOSE 139

CMD ["./entrypoint.sh"]
