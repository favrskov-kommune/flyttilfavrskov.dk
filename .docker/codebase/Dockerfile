FROM composer:1.10.17 as website-build

# Install system dependencies
RUN apk add --no-cache \
  --repository http://dl-cdn.alpinelinux.org/alpine/v3.12/main/ \
  nodejs=12.22.1-r0 \
  npm

RUN apk add --no-cache --virtual .build-deps \
        libxml2-dev \
        shadow \
        autoconf \
        g++ \
        make \
    && apk add --no-cache imagemagick-dev imagemagick \
    && pecl install imagick \
    && apk del .build-deps

RUN echo "extension=imagick.so" > /usr/local/etc/php/conf.d/imagick.ini

RUN apk add --no-cache libpng libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev && docker-php-ext-install gd && apk del libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev
RUN echo "extension=gd.so" > /usr/local/etc/php/conf.d/gd.ini

# Set root folder
WORKDIR /app

# Install nodejs dependencies
ADD webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets/package.json \
  webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets/package-lock.json \
  /app/webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets/
RUN cd /app/webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets \
    && npm ci

# Go to root directory
WORKDIR /app

# Install PHP dependencies
ADD composer.json \
  composer.lock \
  /app/
COPY scripts/composer /app/scripts/composer
#COPY assets /app/assets
#RUN composer install --ignore-platform-reqs --optimize-autoloader --no-dev --no-interaction --no-progress --prefer-dist
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress

# Build frontend
COPY webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets /app/webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets
RUN cd /app/webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets \
    && npm run build:prod

# Clean node_modules for lighter multi-stage build size in images copying from this image
RUN rm -rf /app/webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets/node_modules

# Copy application
COPY . /app
