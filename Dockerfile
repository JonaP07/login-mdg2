# Usamos PHP 8.2 con Apache incluido
FROM php:8.2-apache

# Instalamos la extensión para conectarnos a PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copiamos todo nuestro código dentro del contenedor
COPY . /var/www/html/

# Le damos los permisos correctos
RUN chown -R www-data:www-data /var/www/html

# El contenedor escucha en el puerto 80
EXPOSE 80