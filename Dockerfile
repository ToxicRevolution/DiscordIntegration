FROM php:7.2.5-cli-alpine3.7

RUN docker-php-ext-install pdo pdo_mysql bcmath
COPY . .
CMD ["php", "bot.php"]