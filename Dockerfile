FROM php:fpm-alpine

MAINTAINER Konstantin Grachev <ko@grachev.io>

ENV APP_DIR /usr/local/app

ENV COMPOSER_BIN_DIR /usr/local/bin
ENV COMPOSER_CACHE_DIR /var/cache/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

ENV PATH ${APP_DIR}/bin:${PATH}

WORKDIR ${APP_DIR}

RUN set -ex \
    && apk --no-cache add \
        icu-dev \
        zlib-dev \
    && docker-php-ext-install zip intl pdo_mysql iconv opcache \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer global require phpunit/phpunit

COPY ./composer.* ${APP_DIR}/
RUN composer install --no-dev --no-scripts --no-interaction --no-autoloader --quiet
COPY ./ ${APP_DIR}

RUN set -ex \
    && composer dumpautoload --no-dev --optimize --quiet \
    && { \
        echo 'opcache.enable = 1'; \
        echo 'opcache.enable_cli = 1'; \
        echo 'opcache.memory_consumption = 64'; \
        echo 'opcache.interned_strings_buffer = 4'; \
        echo 'opcache.max_accelerated_files = 15000'; \
        echo 'opcache.max_wasted_percentage = 10'; \
        echo ';opcache.use_cwd = 1'; \
        echo 'opcache.validate_timestamps = 0'; \
        echo ';opcache.revalidate_freq = 2'; \
        echo ';opcache.revalidate_path = 0'; \
        echo 'opcache.save_comments = 1'; \
        echo 'opcache.load_comments = 1'; \
    } > $PHP_INI_DIR/conf.d/opcache.ini \
    && chmod -R 644 ${APP_DIR} \
    && find ${APP_DIR} -type d -exec chmod 755 {} \; \
    && chmod +x -R $APP_DIR/bin/* \
    && { \
        echo '#!/bin/sh'; \
        echo 'set -ex'; \
        echo 'composer run-script post-install-cmd --no-interaction --quiet'; \
        echo 'console doctrine:migrations:migrate --no-interaction --allow-no-migration'; \
        echo 'chown -R www-data:www-data $APP_DIR/var'; \
        echo 'exec "$@"'; \
    } > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
