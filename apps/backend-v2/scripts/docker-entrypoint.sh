#!/bin/sh
set -e

echo "==> Starting VSTEP backend..."

# Check required env vars
if [ -z "$DB_URL" ] && [ -z "$DB_HOST" ]; then
    echo "ERROR: No database configured. Set DB_URL or DB_HOST in Render environment."
    echo "  DB_URL example: postgres://user:pass@ep-xxx.neon.tech/vstep?sslmode=require"
    exit 1
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force --no-interaction
fi

# Cache config & routes for performance
echo "==> Caching config and routes..."
php artisan config:cache
php artisan route:cache

# Run database migrations using direct (non-pooler) connection
echo "==> Running migrations..."
php artisan migrate --force --no-interaction --database=pgsql-migrate

if [ "${DB_SEED_ON_BOOT:-false}" = "true" ]; then
    echo "==> Seeding database..."
    php artisan db:seed --force --no-interaction
else
    echo "==> Skipping database seed (set DB_SEED_ON_BOOT=true to enable)"
fi

echo "==> Starting Octane server..."
exec "$@"
