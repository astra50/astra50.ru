#!/bin/sh

set -e

bin/symfony_requirements

php-cs-fixer fix --dry-run --rules=@Symfony ./src

bin/console doctrine:schema:validate

phpunit
