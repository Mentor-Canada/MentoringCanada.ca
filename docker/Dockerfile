FROM ubuntu:18.04

RUN groupadd -g 1000 ubuntu
RUN useradd -ms /bin/bash ubuntu -u 1000 -g 1000
RUN usermod -aG sudo ubuntu
COPY ./copy/.inputrc /home/ubuntu/.inputrc

RUN useradd -ms /bin/bash macos -u 501 -g 20
RUN usermod -aG sudo macos
COPY ./copy/.inputrc /home/macos/.inputrc

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y

RUN apt-get install sudo -y
COPY ./copy/sudoers /etc/sudoers

RUN apt-get install mysql-client -y
RUN apt-get install rsync -y
RUN apt-get install curl -y
RUN apt-get install npm -y
RUN npm i -g n
RUN n 10
RUN npm i -g npm@6
RUN npm i -g gulp-cli

RUN apt-get install php -y
RUN apt-get install libapache2-mod-php -y
RUN apt-get install php-curl -y
RUN apt-get install php-mysql -y
RUN apt-get install php-zip -y
RUN apt-get install php-mbstring -y
RUN apt-get install php-imagick -y
RUN apt-get install php-intl -y
RUN apt-get install php-xml -y
RUN apt-get install php-xmlrpc -y
RUN apt-get install php-soap -y
RUN apt-get install composer -y
RUN apt-get install php-mailparse -y
RUN apt-get install php-gd -y
RUN apt-get install php-xdebug -y

RUN a2enmod rewrite
RUN a2enmod headers

COPY ./copy/000-default.conf /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80
