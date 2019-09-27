FROM drupal:8.7.7-apache AS base

RUN apt-get update && apt-get install -y \
        git \
        libicu-dev \
        openssh-client \
        sudo \
        libzip-dev \
        vim \
        imagemagick \
        zlib1g-dev && \
    rm -rf /var/lib/apt/lists/*

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
        zip && \
        ( yes | pecl install xdebug )

#Composer setup
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
   && php -r "if (hash_file('sha384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Mismatching hashes'; unlink('composer-setup.php'); }" \
   && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
   && rm composer-setup.php

COPY code/ /src
WORKDIR /src
RUN chown -R www-data:www-data /src /var/www
USER www-data
COPY httpd.conf /etc/apache2/apache2.conf

ENV APP_ENV=staging
RUN composer install --optimize-autoloader
USER root
