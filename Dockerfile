FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y libzip4 libzip-dev zip git

RUN docker-php-ext-install zip

RUN curl --silent --show-error https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer --version=2.8.5
