#!/bin/sh

set -ex

SYMFONY_ENV=${SYMFONY_ENV:=dev}
COMMAND=
XDEBUG=
OPCACHE=
DEVDEPS=
MIGRATION=
FIXTURES=
BUILDPARAMS=
REQUIREMENTS=

for i in "$@"
do
case $i in
    -x|--xdebug)
    XDEBUG=true
    ;;
    --no-xdebug)
    XDEBUG=false
    ;;
    *)
    # unknown option
    COMMAND=$COMMAND' '$i
    ;;
esac
    # past argument=value
    shift
done


if [ "$SYMFONY_ENV" == "dev" ]; then
    XDEBUG=${XDEBUG:=true}
    BUILDPARAMS=${BUILDPARAMS:=true}

    COMMAND=${COMMAND:='bin/console server:run 0.0.0.0:80'}
fi

if [ "$SYMFONY_ENV" == "test" ]; then
    export SYMFONY_DEBUG=0

    BUILDPARAMS=${BUILDPARAMS:=true}
    DEVDEPS=${DEVDEPS:=true}
    REQUIREMENTS=${REQUIREMENTS:=true}
    MIGRATION=${MIGRATION:=true}
    FIXTURES=${FIXTURES:=true}

    COMMAND=${COMMAND:=phpunit}
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    OPCACHE=${OPCACHE:=true}
    DEVDEPS=${DEVDEPS:=false}
    MIGRATION=${MIGRATION:=true}

    COMMAND=${COMMAND:=php-fpm}
fi

ln -s $APP_DIR/bin/console /usr/local/bin/sf
chmod -R 644 ${APP_DIR}
find ${APP_DIR} -type d -exec chmod 755 {} \;
chmod +x -R $APP_DIR/bin/*

if [ "$BUILDPARAMS" == "true" ]; then
    composer run-script build-parameters --no-interaction --quiet
fi

if [ "$DEVDEPS" == "true" ]; then
    composer install --no-interaction --optimize-autoloader
else
    composer install --no-dev --no-interaction --optimize-autoloader
fi

if [ "$REQUIREMENTS" == "true" ]; then
    bin/symfony_requirements
fi

if [ "$MIGRATION" == "true" ]; then
    console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

if [ "$FIXTURES" == "true" ]; then
    bin/console doctrine:fixtures:load --no-interaction --quiet
fi

if [ "$XDEBUG" == "true" ]; then
    docker-php-ext-enable xdebug
fi

if [ "$OPCACHE" == "true" ]; then
    docker-php-ext-enable opcache
fi

if [ "$SYMFONY_ENV" == "prod" ]; then
    chown -R www-data:www-data $APP_DIR/var
    cp -rfL  $APP_DIR/web/* $NGINX_WEB_DIR/
    rm -rf $NGINX_WEB_DIR/*.php
fi

$COMMAND
