FROM php:fpm-alpine

MAINTAINER Konstantin Grachev <me@grachevko.ru>

ENV APP_DIR=/usr/local/app \
  COMPOSER_CACHE_DIR=/var/cache/composer \
  COMPOSER_ALLOW_SUPERUSER=1

ENV PATH=${APP_DIR}/bin:${APP_DIR}/vendor/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apk add --no-cache \
        git \
        icu-dev \
        zlib-dev \
    && docker-php-ext-install zip intl pdo_mysql iconv opcache \
    && rm -rf /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS && pecl install xdebug && apk del .build-deps

ARG SOURCE_DIR=.

COPY $SOURCE_DIR/composer.* ${APP_DIR}/
RUN if [ -f composer.lock ]; then \
    composer install --no-scripts --no-interaction --no-autoloader --no-progress --prefer-dist \
    && rm -rf ${COMPOSER_CACHE_DIR}/* ; fi

COPY $SOURCE_DIR/ ${APP_DIR}/

VOLUME ${APP_DIR}/var/logs
VOLUME ${APP_DIR}/var/sessions

ENTRYPOINT ["docker-entrypoint.sh"]
CMD []
