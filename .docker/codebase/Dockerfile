FROM composer:1.10.17 as website-build

# Install system dependencies
RUN apk add --no-cache \
  --repository http://dl-cdn.alpinelinux.org/alpine/v3.12/main/ \
  nodejs=12.22.1-r0 \
  npm

RUN apk --no-cache add pkg-config
RUN apk --no-cache add autoconf g++ make && pecl install imagick && docker-php-ext-enable imagick && rm -rf /tmp/pear; apk del autoconf g++ make;

#RUN apk --no-cache add imagemagick \
#  && apk --no-cache add file \
#  && pecl install imagick \
#  && pecl install imagick-devel \
#  && docker-php-ext-enable imagick \

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
