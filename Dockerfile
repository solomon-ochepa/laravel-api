# Note: Run `docker build -t mburton3969/php-nodejs:8.3 -f Dockerfile.php-nodejs .` to build the base image

# Base Image
FROM mburton3969/php-nodejs:8.3

# Define the build arguments with default values
ARG APP_ENV="production"
ARG WWWGROUP=1000

# Set Environment Variables
ENV APP_ENV=${APP_ENV}

# Copy Source Code
COPY . /var/www/html/

################################
# Configurations
################################
RUN php -r "file_exists('php.ini') && copy('php.ini', '/usr/local/etc/php/php.ini');"

################################
# Dependencies
################################
# Install PHP & JS Dependencies
RUN composer install --optimize-autoloader --ignore-platform-reqs && \
    pnpm install && pnpm run build

################################
# Path Permissions
################################
RUN chown -R www-data:www-data /var/www/html/
RUN if [ "$APP_ENV" = "local" ]; then \
        groupadd --force -g ${WWWGROUP:-1000} sail && \
        useradd -ms /bin/bash --no-user-group -g ${WWWGROUP:-1000} -u 1337 sail; \
    fi

################################
# Supervisor - Config
################################
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and set permissions for the entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set entrypoint and default command
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
