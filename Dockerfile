FROM php:apache

MAINTAINER Konstantin Grachev <me@grachevko.ru>

ENV APP_DIR=/usr/local/app \
  COMPOSER_CACHE_DIR=/var/cache/composer \
  COMPOSER_ALLOW_SUPERUSER=1

ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apt-get update && apt-get install -y --no-install-recommends \
        git \
        libicu-dev \
    && docker-php-ext-install intl pdo_mysql iconv opcache \
    && rm -rf ${PHP_INI_DIR}/conf.d/docker-php-ext-opcache.ini \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && pecl install xdebug \
    && rm -r /var/lib/apt/lists/*

ARG SOURCE_DIR=.

COPY $SOURCE_DIR/composer.* ${APP_DIR}/
RUN if [ -f composer.lock ]; then \
    composer install --no-scripts --no-interaction --no-autoloader --no-progress --prefer-dist \
    && rm -rf ${COMPOSER_CACHE_DIR}/* ; fi

COPY $SOURCE_DIR/ ${APP_DIR}/

VOLUME ${APP_DIR}/var/logs
VOLUME ${APP_DIR}/var/sessions

ENTRYPOINT ["bash", "bin/docker-entrypoint.sh"]
