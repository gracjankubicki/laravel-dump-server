PHP_VERSION ?= 8.5
IMAGE ?= laravel-dump-server-dev-$(PHP_VERSION)
TESTBENCH ?= ^11.0
VAR_DUMPER ?= ^8.0
RUN = docker run --rm --user $(shell id -u):$(shell id -g) -v "$(CURDIR):/app" -w /app -e COMPOSER_ROOT_VERSION=3.1.x-dev -e GIT_CONFIG_COUNT=1 -e GIT_CONFIG_KEY_0=safe.directory -e GIT_CONFIG_VALUE_0=/app $(IMAGE)
.PHONY: build install test analyse audit check exec composer format format-check rector matrix-case
build:
	docker build --build-arg PHP_VERSION=$(PHP_VERSION) -t $(IMAGE) .
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
format:
	$(RUN) composer format
format-check:
	$(RUN) composer format:check
rector:
	$(RUN) composer rector
matrix-case: build
	@mkdir -p .cache/matrix-composer
	docker run --rm --user $(shell id -u):$(shell id -g) -v "$(CURDIR):/source:ro" -v "$(CURDIR)/.cache/matrix-composer:/tmp/composer" --tmpfs /app:rw,exec,mode=1777 -e COMPOSER_ROOT_VERSION=3.1.x-dev -e TESTBENCH='$(TESTBENCH)' -e VAR_DUMPER='$(VAR_DUMPER)' $(IMAGE) sh /source/tests/matrix-case.sh
