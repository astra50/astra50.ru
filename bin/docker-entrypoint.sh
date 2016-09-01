#!/bin/sh

set -e

APP_DIR=${APP_DIR:=/usr/local/app}
SYMFONY_ENV=${SYMFONY_ENV:=dev}
NGINX_WEB_DIR=${NGINX_WEB_DIR:=/var/www}

COMMAND=
XDEBUG=
OPCACHE=
COMPOSER=
MIGRATION=
FIXTURES=
BUILD_PARAMS=
REQUIREMENTS=

for i in "$@"
do
case ${i} in
    -x|--xdebug)
    XDEBUG=true
    ;;
    --no-xdebug)
    XDEBUG=false
    ;;
    -m|--migrations)
    MIGRATION=true
    ;;
    -f|--fixtures)
    FIXTURES=true
    ;;
    --no-composer)
    COMPOSER=false
    ;;
    composer)
    COMPOSER=false
    XDEBUG=false
    COMMAND=${COMMAND}' '${i}
    ;;
    *)
    # unknown option
    COMMAND=${COMMAND}' '${i}
    ;;
esac
    # past argument=value
    shift
done


if [ "$SYMFONY_ENV" == "dev" ]; then
    XDEBUG=${XDEBUG:=true}
    BUILD_PARAMS=${BUILD_PARAMS:=true}
    DEV_DEPS=${DEV_DEPS:=true}
    COMPOSER=${COMPOSER:="composer install --no-interaction --optimize-autoloader --prefer-source"}

    COMMAND=${COMMAND:='bin/console server:run 0.0.0.0:80'}
fi

if [ "$SYMFONY_ENV" == "test" ]; then
    export SYMFONY_DEBUG=0

    BUILD_PARAMS=${BUILD_PARAMS:=true}
    DEV_DEPS=${DEV_DEPS:=true}
    COMPOSER=${COMPOSER:="composer install --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
    REQUIREMENTS=${REQUIREMENTS:=true}
    MIGRATION=${MIGRATION:=true}
    FIXTURES=${FIXTURES:=true}

    COMMAND=${COMMAND:=phpunit}
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    COMPOSER=${COMPOSER:="composer install --no-dev --no-interaction --optimize-autoloader --no-progress --prefer-dist"}
    MIGRATION=${MIGRATION:=true}
    OPCACHE=${OPCACHE:=true}

    COMMAND=${COMMAND:=php-fpm}
fi

ln -sf ${APP_DIR}/bin/console /usr/local/bin/sf
#chmod -R 644 ${APP_DIR}
#find ${APP_DIR} -type d -exec chmod 755 {} \;
chmod +x -R ${APP_DIR}/bin/*

if [ "$BUILD_PARAMS" == "true" ]; then
    composer run-script build-parameters --no-interaction
fi

if [ "$COMPOSER" != "false" ]; then
    ${COMPOSER}
fi

if [ "$REQUIREMENTS" == "true" ]; then
    bin/symfony_requirements
fi

if [ "$MIGRATION" == "true" ]; then
    console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

if [ "$FIXTURES" == "true" ]; then
    bin/console doctrine:fixtures:load --no-interaction
fi

if [ "$XDEBUG" == "true" ]; then
    docker-php-ext-enable xdebug
fi

if [ "$OPCACHE" == "true" ]; then
    docker-php-ext-enable opcache
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    chown -R www-data:www-data ${APP_DIR}/var
    cp -rfL  ${APP_DIR}/web/* ${NGINX_WEB_DIR}/
    rm -rf ${NGINX_WEB_DIR}/*.php
fi

${COMMAND}
