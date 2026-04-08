FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /opt
RUN composer create-project laravel/laravel:^12.0 app --prefer-dist --no-interaction

WORKDIR /var/www/html
RUN cp -R /opt/app/. /var/www/html/

COPY app ./app
COPY bootstrap ./bootstrap
COPY routes ./routes
COPY database/migrations ./database/migrations
COPY tests ./tests
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY README.md ./README.md

RUN mkdir -p database && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
