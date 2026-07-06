# Use an official PHP image with Apache
FROM php:8.4-apache

# Install system dependencies & PHP extensions needed for Laravel/Filament
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl opcache zip

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Install NodeJS and NPM for compiling front-end assets (Vite + Tailwind)
RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer securely
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all project files to the container
COPY . .

# Change Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build production assets
RUN npm install
RUN npm run build

# Set proper directory storage permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80 for Render
EXPOSE 80

# Run migrations, database seeders, and start Apache server on startup
CMD php artisan migrate --force && php artisan db:seed --force && apache2-foreground
