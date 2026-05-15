FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install gd pdo pdo_mysql

# Limpiar MPMs existentes
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2dismod mpm_prefork || true

# Activar solo uno
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]