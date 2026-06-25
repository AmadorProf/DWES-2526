# Usa una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones de mysqli (necesaria para la conexión a la DB)
# También instalamos gd para posibles tareas de imagen y unzip/git para herramientas
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        git \
        libpq-dev \
        libonig-dev \
        libxml2-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd mysqli pdo_mysql opcache

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Limpiar caché de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar permisos para Apache
RUN chown -R www-data:www-data /var/www/html
