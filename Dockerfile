# Usar una imagen base de PHP con Apache
FROM php:8.1-apache

# Copia el archivo de configuración personalizado de Apache
COPY apache2-custom.conf /etc/apache2/conf-available/apache2-custom.conf

# Habilita el módulo rewrite y el módulo SSL
RUN a2enmod rewrite ssl

# Activa tu configuración personalizada
RUN a2enconf apache2-custom

# Instalar extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instalar nano
RUN apt update && apt upgrade -y && apt install nano -y

# Copiar archivos de la aplicación al directorio raíz de Apache
COPY . /var/www/html

# Configurar permisos si es necesario
RUN chown -R www-data:www-data /var/www/html

# Establecer permisos para el directorio específico
RUN chown -R www-data:www-data /var/www/html && \
    mkdir -p /var/www/html/img/medicine && \
    chown -R www-data:www-data /var/www/html/img/medicine && \
    chmod -R 755 /var/www/html/img/medicine
