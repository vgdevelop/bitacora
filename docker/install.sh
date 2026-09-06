#!/usr/bin/env sh
set -eu

if [ ! -f .env ]; then
    cp .env.example .env
    echo "Se creó .env. Cambia DB_PASSWORD, ADMIN_PASSWORD y APP_URL antes de continuar."
    exit 1
fi

docker compose build
docker compose run --rm app composer install --no-interaction --prefer-dist --optimize-autoloader
docker compose run --rm app php artisan key:generate --force
docker compose run --rm app php artisan migrate --seed --force
docker compose up -d

echo "Bitácora iniciada en el puerto 8000."
