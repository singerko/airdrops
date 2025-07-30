FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    cron \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions with GD support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/airdrop airdrop
RUN mkdir -p /home/airdrop/.composer && \
    chown -R airdrop:airdrop /home/airdrop

# Set working directory
WORKDIR /var/www

# Copy ALL application files first
COPY --chown=airdrop:airdrop . /var/www

# Update Node.js to version 20 (Debian/Ubuntu way)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP dependencies AFTER copying files
RUN cd /var/www && composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Run composer scripts after copying files
RUN composer run-script post-autoload-dump

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/sites-available/default

# Configure Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup cron for scheduled tasks
COPY docker/cron/laravel-scheduler /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler && \
    crontab /etc/cron.d/laravel-scheduler

# Create necessary directories and set permissions
RUN mkdir -p /var/log/supervisor /run/php \
    storage/app/public \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set final permissions
RUN chown -R airdrop:www-data /var/www && \
    chmod -R 775 /var/www/storage && \
    chmod -R 775 /var/www/bootstrap/cache

# Create storage link
RUN php artisan storage:link || true

# Fix permissions for web server user
RUN usermod -a -G airdrop www-data

# Create log file with correct permissions
RUN touch storage/logs/laravel.log && \
    chown airdrop:www-data storage/logs/laravel.log && \
    chmod 664 storage/logs/laravel.log

# Ensure all storage directories are writable
RUN find storage -type d -exec chmod 775 {} \; && \
    find storage -type f -exec chmod 664 {} \; && \
    chown -R airdrop:www-data storage

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
