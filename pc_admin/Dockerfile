FROM debian:stretch-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

RUN apt-get update && apt-get install -y --no-install-recommends expect nano nmap # TODO: rauswerfen wenn fertig
#RUN apt-get install -y --no-install-recommends apache2 php php-ldap php-ssh2 wakeonlan # php-xml ucf
RUN echo "mariadb-server mysql-server/root_password password password" | debconf-set-selections
RUN echo "mariadb-server mysql-server/root_password_again password password" | debconf-set-selections
RUN apt-get update && apt-get install -y --no-install-recommends nginx openssl openssh-client php-fpm php-mysql php-ldap php-ssh2 wakeonlan sudo mariadb-server wget
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY index.html /var/www/html/
COPY pc_admin.tar.gz /root/
COPY setup.sh /root/
COPY default /etc/nginx/sites-enabled/
COPY privkey.pem /etc/nginx/
COPY cacert.pem /etc/nginx/
RUN tar -xzf /root/pc_admin.tar.gz -C /var/www/html
RUN chown -R www-data /var/www/html/
RUN echo "www-data ALL=NOPASSWD: /usr/bin/wakeonlan" >> /etc/sudoers
# TODO: choose a better way for this:
RUN echo "www-data ALL=NOPASSWD: /bin/chmod, /bin/chown" >> /etc/sudoers

COPY htaccess /

EXPOSE 80

COPY entrypoint.sh /
RUN chmod +x /entrypoint.sh

#CMD ["nginx", "-g", "daemon off;"]
CMD ["./entrypoint.sh"]
