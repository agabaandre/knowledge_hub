#!/bin/bash
set -euo pipefail

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache public/uploads \
    /var/log/php /var/log/php-fpm

if [ "${FIX_PERMISSIONS_ON_BOOT:-false}" = "true" ]; then
    echo "Applying recursive ownership/permissions..."
    chown -R www-data:www-data storage bootstrap/cache public/uploads /var/log/php /var/log/php-fpm 2>/dev/null || true
    chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true
fi

WAIT_FOR_MYSQL="${WAIT_FOR_MYSQL:-true}"
MYSQL_WAIT_SECONDS="${MYSQL_WAIT_SECONDS:-180}"
MYSQL_WAIT_INTERVAL_SECONDS="${MYSQL_WAIT_INTERVAL_SECONDS:-2}"

if [ "${WAIT_FOR_MYSQL}" = "true" ]; then
    DB_HOST_SAFE="${DB_HOST:-mysql}"
    DB_PORT_SAFE="${DB_PORT:-3306}"
    DB_USERNAME_SAFE="${DB_USERNAME:-root}"

    echo "Waiting for MySQL at ${DB_HOST_SAFE}:${DB_PORT_SAFE}..."

    # Use mysqladmin ping (faster + doesn't repeatedly boot PHP + PDO).
    end_time=$((SECONDS + MYSQL_WAIT_SECONDS))
    while [ $SECONDS -lt $end_time ]; do
        if mysqladmin ping \
            -h"${DB_HOST_SAFE}" \
            -P"${DB_PORT_SAFE}" \
            -u"${DB_USERNAME_SAFE}" \
            -p"${DB_PASSWORD:-}" \
            --silent >/dev/null 2>&1; then
            echo "MySQL is ready."
            break
        fi
        sleep "${MYSQL_WAIT_INTERVAL_SECONDS}"
    done

    if ! mysqladmin ping \
        -h"${DB_HOST_SAFE}" \
        -P"${DB_PORT_SAFE}" \
        -u"${DB_USERNAME_SAFE}" \
        -p"${DB_PASSWORD:-}" \
        --silent >/dev/null 2>&1; then
        echo "MySQL did not become ready in time (${MYSQL_WAIT_SECONDS}s)." >&2
        exit 1
    fi
fi

if [ "${WAIT_FOR_MEILISEARCH:-true}" = "true" ] && [ "${SCOUT_DRIVER:-}" = "meilisearch" ]; then
    MEILI_HOST="${MEILISEARCH_HOST:-http://meilisearch:7700}"
    echo "Waiting for Meilisearch at ${MEILI_HOST}..."
    for i in $(seq 1 45); do
        if php -r "
            \$host = getenv('MEILISEARCH_HOST') ?: 'http://meilisearch:7700';
            \$ctx = stream_context_create(['http' => ['timeout' => 2]]);
            \$body = @file_get_contents(rtrim(\$host, '/').'/health', false, \$ctx);
            exit(\$body !== false ? 0 : 1);
        " 2>/dev/null; then
            echo "Meilisearch is ready."
            break
        fi
        if [ "$i" -eq 45 ]; then
            echo "Warning: Meilisearch did not become ready in time (search indexing may fail until it is up)." >&2
        fi
        sleep 2
    done
fi

if [ ! -f .env ] && [ -f .env.docker.example ]; then
    cp .env.docker.example .env
fi

if [ ! -d vendor ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ "${GENERATE_APP_KEY_ON_BOOT:-true}" = "true" ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi

php artisan hub:link-storage 2>/dev/null || true

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force --no-interaction
fi

exec docker-php-entrypoint "$@"
