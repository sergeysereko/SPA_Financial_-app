FROM php:8.1-apache

# Установка необходимых инструментов и расширений
RUN apt-get update && apt-get install -y \
    zip unzip git \
    && docker-php-ext-install pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Копирование файлов проекта
COPY ./backend /var/www/html/backend/
COPY ./public /var/www/html/public/

# Настройка Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Установка зависимостей через Composer
WORKDIR /var/www/html/backend/
RUN composer install --no-dev --optimize-autoloader

# Очистка кеша
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка рабочей директории
WORKDIR /var/www/html/