FROM php:8.2-apache

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git libonig-dev mariadb-client && \
    docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/var

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose Apache port
EXPOSE 80