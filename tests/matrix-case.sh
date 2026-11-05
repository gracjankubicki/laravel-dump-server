#!/bin/sh
set -eu
# Each case resolves its own dependencies without changing the source checkout.
mkdir /app/project
tar -C /source --exclude=vendor --exclude=.git --exclude=.cache --exclude=composer.lock --exclude=.phpunit.cache --exclude=storage -cf - . | tar -C /app/project -xf -
cd /app/project
composer update --with "orchestra/testbench:$TESTBENCH" --with "symfony/var-dumper:$VAR_DUMPER" --prefer-dist --no-interaction
composer test
composer analyse
composer audit --no-interaction
composer show laravel/framework
composer show symfony/var-dumper
