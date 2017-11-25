ifeq ($(wildcard .php_cs),)
    php_cs_config = .php_cs.dist
else
    php_cs_config = .php_cs
endif

DOCKER_COMPOSE_VERSION=1.16.1

all: init docker-pull docker-build

init:
	cp -n docker-compose.yml.dist docker-compose.yml || true
	cp -n ./.env.dist ./.env || true
	mkdir -p ./var/null && touch ./var/null/composer.null
un-init:
	rm -rf docker-compose.yml ./.env
re-init: un-init init

do-install: install-app
install: do-install up db-wait migration permissions

update: pull build install
fresh: pull build do-install up db-wait cache flush

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"
docker-hosts-updater:
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

###> GIT ###
pull:
	git fetch origin
	git pull origin $(shell git rev-parse --abbrev-ref HEAD) || true
empty-commit:
	git commit --allow-empty -m "Empty commit."
	git push
###< GIT ###

###> DOCKER
docker-install: docker-install-engine docker-compose-install
docker-install-engine:
	curl -fsSL get.docker.com | sh
	sudo usermod -a -G docker `whoami`
docker-compose-install:
	sudo rm -rf /usr/local/bin/docker-compose /etc/bash_completion.d/docker-compose
	sudo curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
	sudo chmod +x /usr/local/bin/docker-compose
	sudo curl -L https://raw.githubusercontent.com/docker/compose/$(DOCKER_COMPOSE_VERSION)/contrib/completion/bash/docker-compose -o /etc/bash_completion.d/docker-compose

build: docker-build
docker-build:
	docker-compose build
docker-pull:
	docker-compose pull
up:
	docker-compose up -d
serve: up
restart:
	docker-compose restart app
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs:
	docker-compose logs --follow
logs-app:
	docker-compose logs --follow app
logs-mysql:
	docker-compose logs --follow mysql
###< DOCKER ###

###> APP ###
install-app: composer

composer: composer-install
composer-install:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer install --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-run-script:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer run-script symfony-scripts
composer-update:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer update --prefer-dist
	@$(MAKE) permissions > /dev/null
composer-update-lock:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app composer update --lock
	@$(MAKE) permissions > /dev/null

fixtures:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false -e APP_ENV=dev app console doctrine:fixtures:load --fixtures=src/DataFixtures/ORM/ --no-interaction
migration:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:migrate --no-interaction --allow-no-migration
migration-generate:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migrations:generate
	@$(MAKE) permissions > /dev/null
	@$(MAKE) cs
migration-rollback:latest = $(shell docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:latest | tr '\r' ' ')
migration-rollback:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:execute --down --no-interaction $(latest)
migration-diff:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:migration:diff
	@$(MAKE) cs
migration-diff-dry:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:schema:update --dump-sql
schema-update:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console doctrine:schema:update --force

test-command:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console test -vvv

cli: cli-app
cli-app:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app bash
	@$(MAKE) permissions > /dev/null
cli-mysql:
	docker-compose exec mysql bash

check: cs-check phpstan cache schema-check phpunit-check

php-cs-fixer = docker-compose run --rm --no-deps --entrypoint php-cs-fixer -e PHP_CS_FIXER_FUTURE_MODE=1 app

cs:
	$(php-cs-fixer) fix --config $(php_cs_config)
	@$(MAKE) permissions > /dev/null
cs-check:
	$(php-cs-fixer) fix --config=.php_cs.dist --verbose --dry-run
phpstan:
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app php -d memory_limit=-1 vendor/bin/phpstan analyse --level 6 --configuration phpstan.neon src tests
phpunit:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false -e XDEBUG=false app phpunit --debug --stop-on-failure
phpunit-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false -e XDEBUG=false app phpunit
requirements:
	docker-compose run --rm --no-deps -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false -e XDEBUG=false app symfony_requirements
schema-check:
	docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=0 -e FIXTURES=false -e XDEBUG=false app console doctrine:schema:validate

cache: cache-warmup
cache-clear:
	docker-compose run --rm --no-deps --entrypoint sh app -c "rm -rf ./var/cache/dev ./var/cache/test ./var/cache/prod"
	@$(MAKE) permissions > /dev/null
cache-warmup: cache-clear
	docker-compose run --rm --no-deps -e SKIP_ENTRYPOINT=true -e XDEBUG=false app console cache:warmup
	@$(MAKE) permissions > /dev/null

flush: flush-db migration fixtures
flush-db:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true -e XDEBUG=false app  bash -c "console doctrine:database:drop --force || true && console doctrine:database:create"
db-wait:
	docker-compose run --rm -e COMPOSER_SCRIPT=false -e XDEBUG=false app echo OK
###< APP ###
