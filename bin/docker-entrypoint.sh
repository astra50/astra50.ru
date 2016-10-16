#!/bin/sh

set -e

if [ "$SYMFONY_ENV" == "dev" ]; then
    XDEBUG=${XDEBUG:=true}
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --prefer-source"}

    COMMAND=${COMMAND:='bin/console server:run 0.0.0.0:80'}
fi

if [ "$SYMFONY_ENV" == "test" ]; then
    export SYMFONY_DEBUG=0

    OPCACHE=${OPCACHE:=true}
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
    REQUIREMENTS=${REQUIREMENTS:=true}
    MIGRATION=${MIGRATION:=true}
    FIXTURES=${FIXTURES:=true}

    COMMAND=${COMMAND:="php-cs-fixer fix --dry-run --level symfony ./src/ && bin/console doctrine:schema:validate && phpunit"}
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    OPCACHE=${OPCACHE:=true}
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-dev --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
    MIGRATION=${MIGRATION:=true}

    a2enmod rewrite
    COMMAND=${COMMAND:=apache2-foreground}
fi

{
    echo 'date.timezone = UTC';
    echo 'short_open_tag = off';
} > ${PHP_INI_DIR}/php.ini


if [ "$OPCACHE" == "true" ]; then
    {
        echo 'opcache.enable = 1';
        echo 'opcache.enable_cli = 1';
        echo 'opcache.memory_consumption = 64';
        echo 'opcache.interned_strings_buffer = 4';
        echo 'opcache.max_accelerated_files = 15000';
        echo 'opcache.max_wasted_percentage = 10';
        echo ';opcache.use_cwd = 1';
        echo 'opcache.validate_timestamps = 0';
        echo ';opcache.revalidate_freq = 2';
        echo ';opcache.revalidate_path = 0';
        echo 'opcache.save_comments = 1';
        echo 'opcache.load_comments = 1';
    } > ${PHP_INI_DIR}/conf.d/opcache.ini

    docker-php-ext-enable opcache # wait for fix "nm not found"
    echo -e '\n > opcache enabled\n'
fi

if [ "$COMPOSER_EXEC" != "false" ]; then
    ${COMPOSER_EXEC}
fi

if [ "$REQUIREMENTS" == "true" ]; then
    bin/symfony_requirements
fi

if [ "$MIGRATION" == "true" ]; then
    bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --quiet
fi

if [ "$FIXTURES" == "true" ]; then
    bin/console doctrine:fixtures:load --no-interaction
fi

if [ "$XDEBUG" == "true" ]; then
    {
        echo 'xdebug.remote_enable=On';
        echo 'xdebug.remote_autostart=On';
        echo "xdebug.remote_host=$(/sbin/ip route|awk '/default/ { print $3 }')";
        echo 'xdebug.force_display_errors=On';
        echo 'xdebug.file_link_format="phpstorm://open?file=%f&line=%l"';
    } > ${PHP_INI_DIR}/conf.d/xdebug.ini

    docker-php-ext-enable xdebug # wait for fix "nm not found"
    echo -e '\n> xdebug enabled\n'
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    chown -R www-data:www-data ${APP_DIR}/var
    rm -rf ${APP_DIR}/bin/sf ${APP_DIR}/web/config.php ${APP_DIR}/web/app_dev.php
    cp ${APP_DIR}/app/config/apache.conf ${APACHE_CONFDIR}/sites-enabled/000-default.conf
fi

/bin/sh -c "${COMMAND}"
