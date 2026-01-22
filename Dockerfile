# Use official PHP image with Apache
FROM php:8.2-apache

# Copy project files into container
COPY . /var/www/html/

# Install PHP extensions needed for the application
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache rewrite module for .htaccess support
RUN a2enmod rewrite

# Set ServerName to suppress warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set correct permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for web traffic
EXPOSE 80

# Default command to start Apache
CMD ["apache2-foreground"]
