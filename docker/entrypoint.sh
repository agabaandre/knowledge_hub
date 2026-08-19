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

echo "Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
for i in $(seq 1 60); do
    if php -r "
        try {
            new PDO(
                'mysql:host='.getenv('DB_HOST').';port='.getenv('DB_PORT'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => (int) (getenv('DB_CONNECT_TIMEOUT') ?: 2),
                ]
            );
            exit(0);
        } catch (Throwable \$e) {
            exit(1);
        }
    " 2>/dev/null; then
        echo "MySQL is ready."
        break
    fi
    if [ "$i" -eq 60 ]; then
        echo "MySQL did not become ready in time." >&2
        exit 1
    fi
    sleep 2
done

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
