#!/bin/sh
set -e

# Generate APP_KEY if not already set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force --no-interaction
fi

# Cache config & routes for performance
php artisan config:cache
php artisan route:cache

# Run database migrations
php artisan migrate --force --no-interaction

# Execute the main CMD (octane:start)
exec "$@"
