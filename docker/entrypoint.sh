#!/bin/bash

set -e

echo "Running entrypoint.sh in $APP_ENV environment..."

# Copy .env file based on APP_ENV, if not already present
if [ ! -f ".env" ]; then
    echo "Generating .env file for $APP_ENV environment..."
    case "${APP_ENV}" in
        local)
            [ -f .env.example ] && cp .env.example .env
            ;;
        local-dev)
            [ -f .env.dev ] && cp .env.dev .env
            ;;
        local-prod)
            [ -f .env.prod ] && cp .env.prod .env
            ;;
        *)
            echo "Unknown APP_ENV: $APP_ENV. Skipping .env generation."
            ;;
    esac
fi

# Generate app key if missing
if [ -f .env ] && ( ! grep -q '^APP_KEY=' .env || grep -q 'APP_KEY=$' .env ); then
  php artisan key:generate --force
fi

php artisan optimize:clear
php artisan storage:link --force
php artisan vendor:publish --tag=telescope-assets --force
php artisan optimize

# Run pending migrations (optional)
php artisan migrate --force

# Run supervisord with explicit config path in the background
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf -n &

# Start Apache
exec "$@"
