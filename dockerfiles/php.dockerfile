FROM php:8.2-fpm-alpine
 
WORKDIR /var/www/html
 
COPY . .
 
RUN docker-php-ext-install pdo pdo_mysql
 
RUN addgroup -g 1000 cgrd_user && adduser -G cgrd_user -g cgrd_user -s /bin/sh -D cgrd_user

USER cgrd_user