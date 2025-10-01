# Note: Run `docker build -t mburton3969/php-nodejs:8.3 -f docker/Dockerfile.php-nodejs .` to build the base image

# Use the base image
FROM mburton3969/php-nodejs:8.3

# Define the build arguments with default values
ARG APP_ENV="production"
ARG WWWGROUP=1000

# Set environment variables
ENV APP_ENV=${APP_ENV} \
    CI=true

RUN php -r "file_exists('php.ini') && copy('php.ini', '/usr/local/etc/php/php.ini');"

# Copy configuration files
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --chmod=755 docker/startup.sh /usr/local/bin/startup.sh

# Copy application code
COPY --chown=www-data:www-data . .

RUN git config --global --add safe.directory /var/www/html

# Install dependencies using cache if possible
RUN if [ -f "composer.json" ]; then \
        if [ "$APP_ENV" = "prod" ] || [ "$APP_ENV" = "production" ]; then \
            composer install --no-dev --optimize-autoloader --ignore-platform-reqs; \
        else \
            composer install --optimize-autoloader --ignore-platform-reqs; \
        fi; \
    fi && \
    if [ -f "package.json" ]; then \
        if [ "$APP_ENV" = "prod" ] || [ "$APP_ENV" = "production" ]; then \
            pnpm install --prod && pnpm run build; \
        else \
            pnpm install && pnpm run build; \
        fi; \
    fi

# Set proper permissions for startup script
RUN chmod +x /usr/local/bin/startup.sh

# Change ownership of the web root directory
RUN chown -R www-data:www-data /var/www/html/

# Set the startup
ENTRYPOINT ["/usr/local/bin/startup.sh"]

# Default command (will be passed to the entrypoint script)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
