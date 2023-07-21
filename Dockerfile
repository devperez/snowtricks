# Utiliser une image de base avec PHP
FROM php:8.2.0-fpm-alpine

RUN apk update \
    && apk add curl \
    && rm -rf /var/lib/apt/lists/* \
    # installing install-php-extensions
    && curl -sL -o install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod u+x ./install-php-extensions \
    # installing php extensions
    && ./install-php-extensions intl mysqli opcache pdo pdo_mysql pdo_pgsql pgsql sockets zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer
    
EXPOSE 9000

WORKDIR /project

