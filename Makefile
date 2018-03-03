ifeq ($(wildcard ./.php_cs),)
    php_cs_config = .php_cs.dist
else
    php_cs_config = .php_cs
endif

DOCKER_COMPOSE_VERSION=1.18.0
DOCKER_UBUNTU_VERSION=18.01.0~ce-0~ubuntu

all: init docker-pull docker-build

init:
	cp -n docker-compose.yml.dist docker-compose.yml || true
	cp -n ./.env.dist ./.env || true
	cp -n -r ./contrib/* ./ || true
	mkdir -p ./var/null && touch ./var/null/composer.null
un-init:
	rm -rf docker-compose.yml ./.env
re-init: un-init init

do-install: install-app
install: do-install up db-wait migration permissions

update: pull pull-app cache restart
do-fresh: pull pull-app build do-install up db-wait permissions cache restart
fresh: do-fresh flush
fresh-backup: do-fresh drop flush-solr backup-restore migration search-populate admin
refresh: down fresh
refresh-backup: down fresh-backup

permissions:
	docker run --rm -v `pwd`:/app -w /app alpine sh -c "chown $(shell id -u):$(shell id -g) -R ./ && chmod 777 -R ./var || true"
docker-hosts-updater:
	docker run -d --restart=always --name docker-hosts-updater -v /var/run/docker.sock:/var/run/docker.sock -v /etc/hosts:/opt/hosts grachev/docker-hosts-updater

# To prevent idea to adding this phar to *.iml config
vendor-phar-remove:
	rm -rf vendor/twig/twig/test/Twig/Tests/Loader/Fixtures/phar/phar-sample.phar app/vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Tests/Fixtures/includes/ProjectWithXsdExtensionInPhar.phar app/vendor/phpunit/phpunit/tests/_files/phpunit-example-extension/tools/phpunit.d/phpunit-example-extension-1.0.1.phar app/vendor/phar-io/manifest/tests/_fixture/test.phar || true

###> GIT ###
pull:
	git fetch origin
	if git branch -a | fgrep remotes/origin/$(shell git rev-parse --abbrev-ref HEAD); then git pull origin $(shell git rev-parse --abbrev-ref HEAD); fi
empty-commit:
	git commit --allow-empty -m "Empty commit."
	git push
###< GIT ###

###> DOCKER
docker-install: docker-install-engine docker-install-compose
docker-install-engine:
	curl -fsSL get.docker.com | sh
	sudo usermod -a -G docker `whoami`
docker-install-compose:
	sudo rm -rf /usr/local/bin/docker-compose /etc/bash_completion.d/docker-compose
	sudo curl -L https://github.com/docker/compose/releases/download/$(DOCKER_COMPOSE_VERSION)/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose
	sudo chmod +x /usr/local/bin/docker-compose
	sudo curl -L https://raw.githubusercontent.com/docker/compose/$(DOCKER_COMPOSE_VERSION)/contrib/completion/bash/docker-compose -o /etc/bash_completion.d/docker-compose
docker-upgrade: docker-upgrade-engine docker-install-compose
docker-upgrade-engine:
	sudo apt-get remove -y docker-ce && sudo apt-get install docker-ce=$(DOCKER_UBUNTU_VERSION)

build: docker-build
docker-build:
	docker-compose build
docker-pull:
	docker-compose pull
up:
	docker-compose up -d
serve: up
status:
	watch docker-compose ps
cli: cli-app
restart: restart-app
down:
	docker-compose down -v --remove-orphans
terminate:
	docker-compose down -v --remove-orphans --rmi all
logs:
	docker-compose logs --follow
###< DOCKER ###

###> APP ###
app = docker-compose run --rm -e XDEBUG=false -e WAIT_HOSTS=false -e COMPOSER_DISABLE=true app
app-xdebug = docker-compose run --rm -e WAIT_HOSTS=false -e COMPOSER_DISABLE=true app
app-test = docker-compose run --rm -e APP_ENV=test -e APP_DEBUG=1 -e XDEBUG=false -e WAIT_HOSTS=false -e COMPOSER_DISABLE=true app
php = docker-compose run --rm --entrypoint php app -d memory_limit=-1
php-xdebug = docker-compose run --rm --entrypoint docker-entrypoint-xdebug.sh app php -d memory_limit=-1
sh = docker-compose run --rm --entrypoint sh app -c

cli-app:
	$(app) bash
	@$(MAKE) permissions > /dev/null
cli-app-xdebug:
	$(app-xdebug) bash
	@$(MAKE) permissions > /dev/null
restart-app:
	docker-compose restart app
logs-app:
	docker-compose logs --follow app
pull-app:
	docker-compose pull app

install-app: composer

composer = docker-compose run --rm --no-deps -e XDEBUG=false -e MIGRATIONS=false -e WAIT_HOSTS=false app composer
composer: composer-install
composer-install:
	$(composer) install --prefer-dist
	@$(MAKE) permissions > /dev/null
	@$(MAKE) vendor-phar-remove
composer-run-script:
	$(composer) run-script symfony-scripts
composer-update:
	$(composer) update --prefer-dist
	@$(MAKE) permissions > /dev/null
	@$(MAKE) vendor-phar-remove
composer-update-lock:
	$(composer) update --lock
	@$(MAKE) permissions > /dev/null
composer-outdated:
	$(composer) outdated

fixtures: cache-test
	$(app-test) console doctrine:fixtures:load --no-interaction

migration:
	$(app) console doctrine:migration:migrate --no-interaction --allow-no-migration
migration-generate:
	$(app) console doctrine:migrations:generate
	@$(MAKE) permissions > /dev/null
	@$(MAKE) cs
migration-rollback:latest = $(shell $(app) console doctrine:migration:latest | tr '\r' ' ')
migration-rollback:
	$(app) console doctrine:migration:execute --down --no-interaction $(latest)
migration-diff:
	$(app) console doctrine:migration:diff
	@$(MAKE) permissions > /dev/null
	@$(MAKE) cs
migration-diff-dry:
	$(app) console doctrine:schema:update --dump-sql
schema-update:
	$(app) console doctrine:schema:update --force

app-test:
	$(app) console test

product-translate:
	$(app) console product:translate

test-command:
	docker-compose run --rm -e SKIP_ENTRYPOINT=true app console test -vvv

check: cs-check cache-test phpstan schema-check phpunit-check

php-cs-fixer = docker-compose run --rm --no-deps --entrypoint php-cs-fixer -e PHP_CS_FIXER_FUTURE_MODE=1 app

cs:
	$(php-cs-fixer) fix --config $(php_cs_config)
	@$(MAKE) permissions > /dev/null
cs-check:
	$(php-cs-fixer) fix --config=.php_cs.dist --verbose --dry-run
phpstan = vendor/bin/phpstan analyse --level 6 --configuration phpstan.neon src tests
phpstan:
	$(php) $(phpstan)
phpstan-xdebug:
	$(php-xdebug) $(phpstan)
phpunit:
	$(app-test) phpunit --debug --stop-on-failure
phpunit-check:
	$(app-test) phpunit
requirements:
	$(app-test) symfony_requirements
schema-check:
	$(app-test) console doctrine:schema:validate

cache: cache-clear cache-warmup
cache-test: cache-clear-test cache-warmup-test
cache-clear:
	$(sh) 'rm -rf ./var/cache/"$$APP_ENV"' || true
	@$(MAKE) permissions > /dev/null
cache-warmup:
	$(app) console cache:warmup
	@$(MAKE) permissions > /dev/null
cache-clear-test:
	$(sh) 'rm -rf ./var/cache/test'
	@$(MAKE) permissions > /dev/null
cache-warmup-test:
	$(app-test) console cache:warmup
	@$(MAKE) permissions > /dev/null

flush: drop migration fixtures
flush-backup: drop backup-restore migration
drop:
	$(sh) "console doctrine:database:drop --force || true && console doctrine:database:create"
db-wait:
	docker-compose run --rm -e XDEBUG=false app echo OK

###< APP ###

###> MYSQL ###
cli-mysql:
	docker-compose exec mysql bash
restart-mysql:
	docker-compose restart mysql
logs-mysql:
	docker-compose logs --follow mysql

backup-restore:
	test -s ./var/backup.sql.gz || exit 1
	docker-compose exec mysql bash -c "gunzip < /usr/local/app/var/backup.sql.gz | mysql db"
###< MYSQL ###
