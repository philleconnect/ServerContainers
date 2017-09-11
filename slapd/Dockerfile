FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

RUN groupadd -r openldap && useradd -r -g openldap openldap

RUN apt-get update
RUN apt-get install -y --no-install-recommends expect nano nmap procps # TODO: rauswerfen wenn fertig
RUN apt-get install -y --no-install-recommends ldap-utils slapd
RUN apt-get install -y --no-install-recommends samba smbldap-tools # dpkg-dev

#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# When not limiting the open file descritors limit, the memory consumption of
# slapd is absurdly high. See https://github.com/docker/docker/issues/8231
RUN ulimit -n 8192

COPY debconf_slapd /root/
COPY add_user.ldif /root/
COPY schema_convert.conf /root/
# since it is not in /usr/share/doc/samba/examples/LDAP/ in docker:
COPY samba.schema.gz /root/
COPY samba_indices.ldif /root/
COPY smbldap.conf /etc/smbldap-tools/
COPY smbldap_bind.conf /etc/smbldap-tools/
COPY smbconfadd /root/
COPY smbFolders /root/
COPY entrypoint.sh /entrypoint.sh

EXPOSE 389
EXPOSE 636

#ENTRYPOINT ./entrypoint.sh
#CMD ["slapd", "-d", "32768", "-u", "openldap", "-g", "openldap"]
CMD ["./entrypoint.sh"]
