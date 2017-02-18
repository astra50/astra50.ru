#!/bin/sh

set -e

export DOCKER_BRIDGE_IP=$(/sbin/ip route|awk '/default/ { print $3 }')

if [ ! -z "$GITHUB_AUTH_TOKEN" ]; then
    composer config -g github-oauth.github.com ${GITHUB_AUTH_TOKEN}
fi

# Skip entrypoint if running composer, php or sh
case "$1" in
   composer|php|sh) exec "$@" && exit 0;;
esac

case "$SYMFONY_ENV" in
   prod|dev|test) ;;
   *) >&2 echo env "SYMFONY_ENV" must be in \"prod, dev, test\" && exit 1;;
esac

case "$SYMFONY_DEBUG" in
   0) ;;
   1) touch ${APP_DIR}/web/config.php;;
   *) >&2 echo env "SYMFONY_DEBUG" must be in \"1, 0\" && exit 1;;
esac

COMMAND="$@"
COMPOSER_DEFAULT_EXEC=${COMPOSER_DEFAULT_EXEC:="composer install --no-interaction --prefer-dist"}

if [ "$SYMFONY_ENV" == "dev" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --optimize-autoloader --verbose --profile"}

    XDEBUG=${XDEBUG:=true}
    OPCACHE=${OPCACHE:=false}
    APCU=${APCU:=false}

    COMMAND=${COMMAND:=php-server}

elif [ "$SYMFONY_ENV" == "test" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --apcu-autoloader --no-progress"}

	REQUIREMENTS=${REQUIREMENTS:=true}
	FIXTURES=${FIXTURES:=true}

	COMMAND=${COMMAND:=test}

elif [ "$SYMFONY_ENV" == "prod" ]; then
    COMPOSER_EXEC=${COMPOSER_EXEC:="$COMPOSER_DEFAULT_EXEC --no-dev --apcu-autoloader --no-progress"}

    COMMAND=${COMMAND:=apache}
fi

OPCACHE=${OPCACHE:=true}
APCU=${APCU:=true}
MIGRATION=${MIGRATION:=true}

enableExt() {
    extension=$1
    docker-php-ext-enable ${extension}
    echo -e " > $extension enabled"
}

if [ "$OPCACHE" == "true" ]; then
    enableExt opcache
fi

if [ "$APCU" == "true" ]; then
    enableExt apcu
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
    enableExt xdebug
fi

if [ -f ${APP_DIR}/web/config.php ]; then
	sed -i "s~'::1',~'::1', '$DOCKER_BRIDGE_IP',~g" "$APP_DIR/web/config.php"
fi

exec "$COMMAND"
