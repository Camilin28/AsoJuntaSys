FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    zip \
    unzip \
    && docker-php-ext-install gd pdo pdo_mysql zip curl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8080

CMD ["php", "-d", "upload_max_filesize=10M", "-d", "post_max_size=12M", "-d", "display_errors=Off", "-d", "log_errors=On", "-S", "0.0.0.0:8080", "-t", "."]