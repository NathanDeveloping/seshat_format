FROM php:latest

RUN apt-get update

RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6 

RUN echo "deb http://repo.mongodb.org/apt/debian jessie/mongodb-org/3.4 main" | tee /etc/apt/sources.list.d/mongodb-org-3.4.list

RUN apt-get update

RUN apt-get install -y libpq-dev libsasl2-dev libssl-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

RUN pecl install mongodb && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongo.ini
