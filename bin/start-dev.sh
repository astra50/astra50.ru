#!/bin/sh

set -e

bin/console server:run 0.0.0.0:80 --router="vendor/symfony/symfony/src/Symfony/Bundle/FrameworkBundle/Resources/config/router_prod.php"
