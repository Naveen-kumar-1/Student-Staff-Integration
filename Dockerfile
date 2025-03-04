# Use the official PHP image with Apache
FROM php:8.0-apache

# Enable necessary PHP extensions (e.g., mysqli if you use MySQL)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Copy all files from your local directory to the container
COPY . /var/www/html

# Set appropriate permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Install Composer dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Expose port 80 for Apache
EXPOSE 80
