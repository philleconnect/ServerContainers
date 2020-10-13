FROM debian:buster-slim

MAINTAINER Dirk Winkel <it@polarwinkel.de>

ENV DEBIAN_FRONTEND noninteractive

ENV VERSION "1"

USER root

RUN apt-get update && apt-get install -y --no-install-recommends nginx openssl openssh-client sudo mariadb-client ca-certificates iputils-ping python3 python3-ldap3 python3-flask python3-requests python3-passlib python3-bcrypt python3-pip python3-setuptools python3-flask-login python3-schedule python3-pexpect python3-paramiko python3-scp ldap-utils uwsgi uwsgi-plugin-python3
RUN pip3 install Flask-Session mysql-connector
RUN apt-get purge -y python3-pip python3-setuptools
RUN apt-get autoremove -y

COPY db.sql /
COPY content_initial.sql /
COPY default /etc/nginx/sites-enabled/
COPY scripts/* /usr/local/bin/

RUN mkdir /usr/local/bin/api
ADD api /usr/local/bin/api

RUN chmod +x /usr/local/bin/dirpermissions.py
RUN chmod -R +x /usr/local/bin/api

ADD ui /var/www/html
RUN chown -R www-data /var/www/html/

RUN mkdir /etc/pc_admin
COPY config.txt /etc/pc_admin/

RUN mkdir /etc/pc_admin/ipfire
COPY ipfire/config /etc/pc_admin/ipfire/

RUN echo "www-data ALL=NOPASSWD: /usr/local/bin/dirpermissions.py" >> /etc/sudoers

COPY protocols /etc/

EXPOSE 80
EXPOSE 443

COPY entrypoint.sh /
RUN chmod +x /entrypoint.sh

CMD ["./entrypoint.sh"]
