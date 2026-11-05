ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli
RUN set -eu; apt-get update; apt-get install -y --no-install-recommends git unzip libxml2-dev libonig-dev libsqlite3-dev; \
    missing=""; for extension in dom mbstring xml xmlwriter pdo_sqlite; do \
        if ! php -r "exit(extension_loaded('$extension') ? 0 : 1);"; then missing="$missing $extension"; fi; \
    done; if [ -n "$missing" ]; then docker-php-ext-install $missing; fi \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
ENV COMPOSER_HOME=/tmp/composer
