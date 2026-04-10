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

# Run database migrations
echo "==> Running migrations..."
php artisan migrate --force --no-interaction

echo "==> Starting Octane server..."
exec "$@"
