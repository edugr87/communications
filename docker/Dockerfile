FROM php:7.3-apache

RUN apt-get update && apt-get install -y \
        curl \
        iproute2 \
        libjpeg-dev \
        libfreetype6-dev \
        libpng-dev \
        libxml2-dev \
    && pecl install xdebug-2.7.0 \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
        gd \
        opcache \
        mbstring \
        pdo_mysql \
        soap \
        && docker-php-ext-enable \
            opcache \
            xdebug

COPY apache /etc/apache2/
COPY php /usr/local/etc/php/

RUN a2enmod rewrite
RUN a2enmod ssl
RUN a2enmod headers
RUN a2enmod proxy_http
RUN service apache2 restart
RUN ip -4 route list match 0/0 | awk '{print $3 " host.docker.internal"}' >> /etc/hosts