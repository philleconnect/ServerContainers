FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

RUN groupadd -r openldap && useradd -r -g openldap openldap

#RUN apt-get update
# apt-get update &&
RUN apt-get update && apt-get install -y --no-install-recommends expect nano vim nmap procps
# TODO: rauswerfen wenn fertig
RUN apt-get update && apt-get install -y --no-install-recommends ldap-utils slapd
# libpam-ldap
# libnss-ldap
# dpkg-dev

#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# When not limiting the open file descritors limit, the memory consumption of
# slapd is absurdly high. See https://github.com/docker/docker/issues/8231
RUN ulimit -n 8192

COPY debconf_slapd /root/
#COPY cn=samba.ldif /root/
COPY add_user.ldif /root/
COPY limit.ldif /root/
COPY schema_convert.conf /root/
#RUN zcat /usr/share/doc/samba-doc/examples/LDAP/samba.schema.gz > /etc/ldap/schema/samba.schema
# since it is not in /usr/share/doc/samba/examples/LDAP/ in docker:
COPY samba.schema.gz /etc/ldap/schema/
RUN zcat /etc/ldap/schema/samba.schema.gz > /etc/ldap/schema/samba.schema
COPY samba_indices.ldif /root/
COPY ldapns.schema /etc/ldap/schema/
COPY entrypoint.sh /entrypoint.sh

EXPOSE 389
EXPOSE 636

CMD ["./entrypoint.sh"]
