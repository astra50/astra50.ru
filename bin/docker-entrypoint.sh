#!/bin/sh

set -e

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    echo "machine github.com login $GITHUB_AUTH_TOKEN" > ~/.netrc
fi

# Skip entrypoint if running composer, php or sh
if echo "$1" | grep -q -E '^(composer|php|sh)$'; then
	exec "$@"

	exit 0
fi

case $SYMFONY_ENV in
   prod|dev|test)
	;;
   *)
	>&2 echo env "SYMFONY_ENV" must be in \"prod, dev, test\"
	exit 1
	;;
esac

case $SYMFONY_DEBUG in
   0)
	;;
   1)
	touch ${APP_DIR}/web/config.php
	;;
   *)
	>&2 echo env "SYMFONY_DEBUG" must be in \"1, 0\"
	exit 1
	;;
esac

export HOST_MACHINE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ "$SYMFONY_ENV" == "dev" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --prefer-dist --verbose --profile"}
    XDEBUG=${XDEBUG:=true}

    COMMAND=${COMMAND:=start-dev.sh}

elif [ "$SYMFONY_ENV" == "prod" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-dev --no-interaction --optimize-autoloader --no-progress --prefer-dist"}

    COMMAND=${COMMAND:=start-apache2.sh}

elif [ "$SYMFONY_ENV" == "test" ]; then
	COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
	REQUIREMENTS=${REQUIREMENTS:=true}
	FIXTURES=${FIXTURES:=true}

	COMMAND=${COMMAND:=start-test.sh}
fi

OPCACHE=${OPCACHE:=true}
MIGRATION=${MIGRATION:=true}

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

    docker-php-ext-enable opcache
    echo -e '\n > opcache enabled\n'
fi

if [ "$COMPOSER_EXEC" != "false" ]; then
    ${COMPOSER_EXEC}
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
        echo "xdebug.remote_host=$HOST_MACHINE_IP";
        echo 'xdebug.force_display_errors=On';
        echo 'xdebug.file_link_format="phpstorm://open?file=%f&line=%l"';
    } > ${PHP_INI_DIR}/conf.d/xdebug.ini

    docker-php-ext-enable xdebug
    echo -e '\n> xdebug enabled\n'
fi

if [ -f $APP_DIR/web/config.php ]; then
	sed -i "s~'::1',~'::1', '$HOST_MACHINE_IP',~g" "$APP_DIR/web/config.php"
fi

exec $COMMAND
