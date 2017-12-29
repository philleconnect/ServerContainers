FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

#RUN apt-get install -y --no-install-recommends expect nano nmap ldap-utils # TODO: rauswerfen wenn fertig
#RUN apt-get install -y --no-install-recommends phpldapadmin # package was removed from debian stretch before freeze due to delayed fixing 
RUN apt-get update && apt-get install -y --no-install-recommends wget apache2 php php-ldap php-xml
#ucf

#RUN wget http://ftp.de.debian.org/debian/pool/main/p/phpldapadmin/phpldapadmin_1.2.2-6.1_all.deb
COPY phpldapadmin_1.2.2-6.1_all.deb /root/
RUN apt-get -f install /root/phpldapadmin_1.2.2-6.1_all.deb

#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#COPY debconf_libpam /root/
COPY entrypoint.sh /

EXPOSE 80

#ENTRYPOINT ./entrypoint.sh
CMD ["./entrypoint.sh"]
