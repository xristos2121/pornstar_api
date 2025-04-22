FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev zip nano python3 python3-pip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Ansible
RUN pip3 install --break-system-packages ansible

# Set PHP memory limit
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory-limit.ini

WORKDIR /var/www

CMD ["php-fpm"]
