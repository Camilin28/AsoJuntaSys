FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install gd pdo pdo_mysql

COPY . /var/www/html/

RUN a2dismod mpm_event
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]