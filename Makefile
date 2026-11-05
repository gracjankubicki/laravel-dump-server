IMAGE ?= laravel-dump-server-dev
RUN = docker run --rm --user $(shell id -u):$(shell id -g) -v "$(CURDIR):/app" -w /app -e COMPOSER_ROOT_VERSION=3.0.x-dev -e GIT_CONFIG_COUNT=1 -e GIT_CONFIG_KEY_0=safe.directory -e GIT_CONFIG_VALUE_0=/app $(IMAGE)
.PHONY: build install test analyse audit check exec composer
build:
	docker build -t $(IMAGE) .
install:
	$(RUN) composer update --prefer-dist --no-interaction
composer:
	$(RUN) composer $(CMD)
test:
	$(RUN) composer test
analyse:
	$(RUN) composer analyse
audit:
	$(RUN) composer audit --no-interaction
check:
	$(RUN) composer check
exec:
	$(RUN) $(CMD)
