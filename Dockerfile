FROM php:8.5-apache

RUN adduser --disabled-password --home /home/container container
ENV USER=container HOME=/home/container
WORKDIR /home/container
RUN mkdir /home/container/src

ENV APACHE_DOCUMENT_ROOT /home/container/src

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./uploads.ini $PHP_INI_DIR/conf.d/uploads.ini

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY ./entrypoint.sh /entrypoint.sh

ENTRYPOINT ["/bin/bash", "/entrypoint.sh"]