FROM php:8-alpine

WORKDIR /app

# Installing mongodb, PDO and sockets extensions
RUN apk add --no-cache --virtual build-deps autoconf gcc make musl-dev \
  && apk add --no-cache openssl-dev postgresql-dev \
  && pecl install mongodb-1.10.0 \
  && pecl install xdebug \
  && docker-php-ext-enable mongodb xdebug \
  && docker-php-ext-install pdo_pgsql sockets \
  && apk del --no-cache build-deps

# Downloading wait-for
RUN wget -q https://raw.githubusercontent.com/eficode/wait-for/db2479e4411441e8edc9188d002ee73b3d3e8e4e/wait-for -O /usr/local/bin/wait-for \
    && chmod +x /usr/local/bin/wait-for

# Private packages
RUN mkdir -p /srv/framework/core \
  && echo "xdebug.cli_color=2" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY stubs/ /srv/framework/

# Installing composer dependencies
COPY . .
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer update --no-dev --ignore-platform-reqs

EXPOSE 80
CMD ["composer", "start"]
