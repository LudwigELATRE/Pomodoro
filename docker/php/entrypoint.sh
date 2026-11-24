#!/bin/sh
set -e

# Les clés JWT sont montées en lecture seule (propriétaire hôte, mode 600) :
# on les copie avec les bons droits pour www-data.
if [ -d /run/jwt ]; then
    mkdir -p config/jwt
    cp /run/jwt/private.pem /run/jwt/public.pem config/jwt/
    chown www-data:www-data config/jwt/*.pem
    chmod 600 config/jwt/private.pem
fi

mkdir -p var/cache var/log
chown -R www-data:www-data var

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
    su -s /bin/sh www-data -c "php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration"
fi
su -s /bin/sh www-data -c "php bin/console cache:warmup"

exec docker-php-entrypoint "$@"
