FROM php:8.2-fpm

# Instalar dependencias del sistema y Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de Node y compilar assets
RUN npm install && npm run build

# Configurar permisos
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public

# Exponer puerto
EXPOSE 9000

CMD ["php-fpm"]
