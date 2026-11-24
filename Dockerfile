# syntax=docker/dockerfile:1
# Suit les branches node 22 / PHP 8.4 : le workflow de déploiement reconstruit
# l'image chaque nuit avec --pull pour récupérer les correctifs de sécurité.

FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json webpack.config.js postcss.config.js tailwind.config.js ./
RUN npm ci

COPY assets ./assets
COPY templates ./templates
RUN npm run build

FROM php:8.4-cli-bookworm AS vendor

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libicu-dev \
    && docker-php-ext-install intl pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-progress --no-interaction

COPY . .
# .env de l'image : uniquement APP_ENV (voir docker/env.prod)
COPY docker/env.prod .env
RUN composer dump-autoload --classmap-authoritative --no-dev --no-interaction

FROM php:8.4-apache-bookworm AS runtime

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev \
    && docker-php-ext-install intl pdo_mysql opcache \
    && a2enmod rewrite headers expires \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo 'expose_php = Off' > "$PHP_INI_DIR/conf.d/zz-hardening.ini" \
    && sed -ri 's/^ServerTokens .*/ServerTokens Prod/; s/^ServerSignature .*/ServerSignature Off/' /etc/apache2/conf-available/security.conf \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/entrypoint.sh /usr/local/bin/pomodoro-entrypoint
COPY --from=vendor /app ./
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p var/cache var/log config/jwt \
    && chown -R www-data:www-data var \
    && chmod +x /usr/local/bin/pomodoro-entrypoint

ENV APP_ENV=prod
ENV APP_DEBUG=0

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1/") === false ? 1 : 0);'

ENTRYPOINT ["pomodoro-entrypoint"]
CMD ["apache2-foreground"]
