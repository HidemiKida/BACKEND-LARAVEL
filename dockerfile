# Usa una imagen base de PHP con Composer y extensiones necesarias
FROM php:8.2-fpm

# Instala dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo_pgsql

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Configura Git como seguro
RUN git config --global --add safe.directory /var/www/html

# Copia los archivos del proyecto
COPY . .

# Da permisos al directorio
RUN chown -R www-data:www-data /var/www/html && chmod -R 775 /var/www/html

# Establece la variable de entorno para permitir Composer como root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Limpia dependencias y la caché antes de instalar
RUN rm -rf vendor && composer clear-cache && composer install --optimize-autoloader --no-dev

# Expone el puerto para la aplicación
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
