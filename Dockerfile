FROM php:8.5-cli

RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
        git \
        supervisor \
    && docker-php-ext-install \
        pdo \
        pdo_sqlite \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN useradd -m -d /home/container container

ENV USER=container
ENV HOME=/home/container

WORKDIR /home/container

RUN mkdir -p \
    /home/container/src \
    /home/container/uploads 

COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ADD ./supervisord.conf /etc/supervisor/supervisord.conf

USER container

ENTRYPOINT ["/entrypoint.sh"]