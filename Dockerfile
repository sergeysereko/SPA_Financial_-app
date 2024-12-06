FROM php:8.1-apache

# Установка необходимых расширений
RUN docker-php-ext-install pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование исходного кода
COPY ./backend /var/www/html/
WORKDIR /var/www/html

# Установка зависимостей
RUN composer install
