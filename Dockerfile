FROM php:8.3-alpine

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    @composer-2 xmlwriter xmlreader simplexml zip gd 

WORKDIR /opt

COPY composer.json .

RUN composer install --no-dev --prefer-dist && mkdir src

COPY src src

RUN chmod +x ./src/index.php

ENTRYPOINT [ "/opt/src/index.php" ]
