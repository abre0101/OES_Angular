FROM php:8.2-apache

# Fix Apache MPM configuration
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# Install mysqli and other required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable mysqli

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
