FROM drupal:8.8.1-apache AS base
RUN apt-get update && apt-get install -y \
        git \
        libicu-dev \
        libmagickwand-dev \
        libmagickcore-dev \
        openssh-client \
        sudo \
        libzip-dev \
        vim \
        curl \
        imagemagick \
        msmtp \
        mysql-client \
        cron \
        wget \
        sensible-mda \
        zlib1g-dev && \
    rm -rf /var/lib/apt/lists/*

# Install node & npm for themes
RUN curl -sL https://deb.nodesource.com/setup_12.x | sudo -E bash -
RUN apt install -y nodejs

# Enable Apache modules and install PHP extensions
RUN a2enmod rewrite && \
    a2enmod headers && \
    docker-php-ext-configure calendar && \
    docker-php-ext-install \
        bcmath \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        calendar \
        zip

RUN pecl install imagick && docker-php-ext-enable imagick

RUN pecl install redis && docker-php-ext-enable redis

COPY php.ini /usr/local/etc/php/conf.d/php-overwrite.ini

#Shell setup
RUN echo 'alias ll="ls -lah"' >> ~/.bashrc
RUN echo "export PATH=\$PATH:/src/vendor/drush/drush" >> ~/.bashrc

#Composer setup
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
   && php -r "if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Mismatching hashes'; unlink('composer-setup.php'); }" \
   && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
   && rm composer-setup.php

COPY code/ /src
WORKDIR /src

COPY code/entrypoint.sh .
USER root

RUN chmod +x entrypoint.sh
RUN chown -R www-data:www-data /src
RUN chown www-data:www-data /var/www

USER www-data
COPY apache2.conf /etc/apache2/apache2.conf
ARG env
ENV APP_ENV=$env


FROM base AS https
ENV APP_ENV=$env
ARG github_token
RUN composer config -g github-oauth.github.com $github_token
# Install libraries
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress
RUN cd webroot/sites/flyttilfavrskov.dk/themes/custom/dds_premium/build-assets/ &&\
    npm ci && \
    npm run build:prod
USER root
ENTRYPOINT ["./entrypoint.sh", "https"]

FROM base AS http
USER root
ENTRYPOINT ["./entrypoint.sh", "http"]
