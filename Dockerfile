FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    cron

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/airdrop airdrop
RUN mkdir -p /home/airdrop/.composer && \
    chown -R airdrop:airdrop /home/airdrop

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www
COPY --chown=airdrop:airdrop . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup cron for scheduled tasks
COPY docker/cron/laravel-scheduler /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler
RUN crontab /etc/cron.d/laravel-scheduler

# Set permissions
RUN chown -R airdrop:www-data /var/www
RUN chmod -R 775 /var/www/storage
RUN chmod -R 775 /var/www/bootstrap/cache

# Create directories
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /run/php

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
