FROM php:8.3-fpm-alpine

# Install packages we want to keep
RUN apk --no-cache add \
      libxml2-dev \
      libbz2 \
      zlib-dev

# Install dependencies that are only used during installation of other packages.
# Afterwards it will be cleaned up.
RUN set -xe \
    && apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS \
      coreutils \
      bzip2-dev \
    && docker-php-ext-install -j"$(/usr/bin/nproc)" opcache bcmath bz2 \
    && apk del .build-deps

COPY . /var/www

COPY ./scripts/docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY ./scripts/docker/php/php.ini /usr/local/etc/php/conf.d/additional-php.ini

# Add nginx user for unix socket permission
RUN addgroup -S nginx \
  && adduser -S -D -G nginx nginx

WORKDIR /var/www

CMD ["php-fpm"]