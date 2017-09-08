FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

RUN apt-get update && apt-get install -y --no-install-recommends expect nano nmap # TODO: rauswerfen wenn fertig
#RUN apt-get install -y --no-install-recommends apache2 php php-ldap php-ssh2 wakeonlan # php-xml ucf
RUN echo "mariadb-server mysql-server/root_password password password" | debconf-set-selections
RUN echo "mariadb-server mysql-server/root_password_again password password" | debconf-set-selections
RUN apt-get update && apt-get install -y --no-install-recommends nginx php-fpm php-mysql php-ldap php-ssh2 wakeonlan mariadb-server
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY schoolconnect_admin.tar.gz /root/
COPY default /etc/nginx/sites-enabled/
RUN tar -xzf /root/schoolconnect_admin.tar.gz -C /var/www/html
RUN chown -R www-data /var/www/html/

EXPOSE 80

COPY entrypoint.sh /

#CMD ["nginx", "-g", "daemon off;"]
CMD ["/entrypoint.sh"]
