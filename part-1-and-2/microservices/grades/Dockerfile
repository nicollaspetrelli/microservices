FROM php:8-alpine

WORKDIR /app
COPY . .

# Installing redis extension
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis-5.3.4 \
    && docker-php-ext-enable redis \
    && apk del --purge $PHPIZE_DEPS

# Installing PDO and Postgre driver
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Installing ext-sockets for amqp client
RUN docker-php-ext-install sockets

# Private packages
RUN mkdir -p /srv/framework
COPY --from=ms/libs /app /srv/framework

# Installing composer dependencies
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev

EXPOSE 80
CMD ["composer", "start"]
