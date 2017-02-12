#!/bin/sh

set -e

export DOCKER_BRIDGE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    composer config -g github-oauth.github.com ${GITHUB_AUTH_TOKEN}
fi

# Skip entrypoint if running composer, php or sh
if echo "$1" | grep -q -E '^(composer|php|sh)$'; then
	exec "$@"

	exit 0
fi

case ${SYMFONY_ENV} in
   prod|dev|test)
	;;
   *)
	>&2 echo env "SYMFONY_ENV" must be in \"prod, dev, test\"
	exit 1
	;;
esac

case ${SYMFONY_DEBUG} in
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

if [ "$SYMFONY_ENV" == "dev" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --prefer-dist --verbose --profile"}
    XDEBUG=${XDEBUG:=true}

    COMMAND=${COMMAND:=start-develop}

elif [ "$SYMFONY_ENV" == "prod" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-dev --no-interaction --optimize-autoloader --no-progress --prefer-dist"}

    COMMAND=${COMMAND:=start-apache}

elif [ "$SYMFONY_ENV" == "test" ]; then
	COMPOSER_EXEC=${COMPOSER_EXEC:="composer install --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
	REQUIREMENTS=${REQUIREMENTS:=true}
	FIXTURES=${FIXTURES:=true}

	COMMAND=${COMMAND:=start-testing}
fi

OPCACHE=${OPCACHE:=true}
MIGRATION=${MIGRATION:=true}

ext-enable() {
    extension=$1
    docker-php-ext-enable ${extension}
    echo -e " > $extension enabled"
}

if [ "$OPCACHE" == "true" ]; then
    ext-enable opcache
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
    ext-enable xdebug
fi

if [ -f ${APP_DIR}/web/config.php ]; then
	sed -i "s~'::1',~'::1', '$DOCKER_BRIDGE_IP',~g" "$APP_DIR/web/config.php"
fi

exec ${COMMAND}
