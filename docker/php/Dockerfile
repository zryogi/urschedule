FROM php:8.4-apache

# Install system dependencies for pdo_pgsql and the extension itself
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# The pdo_pgsql extension should be automatically enabled by docker-php-ext-install.
# If not, you might need to uncomment the following line or add a custom php.ini:
RUN echo "extension=pdo_pgsql" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini
