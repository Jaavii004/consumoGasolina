# Utiliza la imagen base de PHP con Apache
FROM buig/php74sf:latest
#FROM php:7.4-apache

# Instala la extensión MySQLi
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
