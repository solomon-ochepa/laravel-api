#!/bin/bash
set -e

cd /var/www/html

echo "Starting Laravel application setup..."

# Check if .env exists, if not create from appropriate environment file
if [ ! -f .env ]; then
    if [ "$APP_ENV" = "local-dev" ] && [ -f .env.dev ]; then
        cp .env.dev .env
        echo "Copied .env.dev to .env"
    elif [ "$APP_ENV" = "local-prod" ] && [ -f .env.prod ]; then
        cp .env.prod .env
        echo "Copied .env.prod to .env"
    else
        cp .env.example .env
        echo "Copied .env.example to .env"
    fi
fi

# Generate application key if not already set
if [ -f .env ] && ! grep -q "^APP_KEY=.\+" .env; then
    php artisan key:generate --force
    echo "Generated application key"
fi

# Create storage link
php artisan storage:link --force

# Clear cached views
php artisan view:clear

# Publish Telescope assets if Telescope is installed
if composer show laravel/telescope >/dev/null 2>&1; then
    php artisan vendor:publish --tag=telescope-assets --force
fi

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Laravel setup completed successfully"

# Execute the main process (passed as arguments to this script)
exec "$@"
