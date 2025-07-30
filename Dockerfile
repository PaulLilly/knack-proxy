

# Use an official PHP image with Apache
FROM php:8.1-apache

# Copy the PHP file into the default web directory
COPY knack-proxy.php /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
