#!/bin/bash

# Mount local cache files out of webroot
if [[ ! -d /mnt/cache/php ]]; then
    mkdir /mnt/cache/php
fi
rm -rf /app/webroot/sites/flyttilfavrskov.dk/files/php/*
mount -o bind /mnt/cache/php /app/webroot/sites/flyttilfavrskov.dk/files/php

if [[ ! -d /mnt/cache/js ]]; then
    mkdir /mnt/cache/js
fi
rm -f /app/webroot/sites/flyttilfavrskov.dk/files/js/optimized/.htaccess
rm -rf /app/webroot/sites/flyttilfavrskov.dk/files/js/*
mount -o bind /mnt/cache/js /app/webroot/sites/flyttilfavrskov.dk/files/js

if [[ ! -d /mnt/cache/css ]]; then
    mkdir /mnt/cache/css
fi
rm -f /app/webroot/sites/flyttilfavrskov.dk/files/css/optimized/.htaccess
rm -rf /app/webroot/sites/flyttilfavrskov.dk/files/css/*
mount -o bind /mnt/cache/css /app/webroot/sites/flyttilfavrskov.dk/files/css

if [[ ! -d /mnt/cache/styles ]]; then
    mkdir /mnt/cache/styles
fi
rm -rf /app/webroot/sites/flyttilfavrskov.dk/files/styles/*
mount -o bind /mnt/cache/styles /app/webroot/sites/flyttilfavrskov.dk/files/styles

echo "Cache mounts complete."

# Mount logs dir
if [[ ! -d /app/logs ]]; then
    mkdir /app/logs
fi

rm -rf /app/logs/*  /app/logs/.keep
mount -o bind /mnt/logs /app/logs

echo "Logs mounts complete."

# Configure NewRelic
if [ "$NEWRELIC_LICENSE_KEY" != "" -a "$NEWRELIC_PROJECT_NAME" != "" ] ; then sed -i \
      -e "s/REPLACE_WITH_REAL_KEY/${NEWRELIC_LICENSE_KEY}/" \
      -e "s/newrelic.appname = \"PHP Application\"/newrelic.appname = \"${NEWRELIC_PROJECT_NAME}\"/" \
      -e "s/;newrelic.daemon.app_connect_timeout =.*/newrelic.daemon.app_connect_timeout=15s/" \
      -e "s/;newrelic.daemon.start_timeout =.*/newrelic.daemon.start_timeout=5s/" \
      /usr/local/etc/php/conf.d/newrelic.ini ;
fi

if [ "$SMTP_PORT" != "" -a "$SMTP_HOST" != "" -a "$SMTP_AUTH_USERNAME" != "" -a "$SMTP_AUTH_PASSWORD" != "" ] ; then sed -i \
      -e "s/REPLACE_WITH_REAL_SMTP_PORT/${SMTP_PORT}/" \
      -e "s/REPLACE_WITH_REAL_SMTP_HOST/${SMTP_HOST}/" \
      -e "s/REPLACE_WITH_REAL_SMTP_AUTH_USERNAME/${SMTP_AUTH_USERNAME}/" \
      -e "s/REPLACE_WITH_REAL_SMTP_AUTH_PASSWORD/${SMTP_AUTH_PASSWORD}/" \
      /root/.msmtprc ;
fi

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php "$@"
fi

# Run the command passed
exec "$@"
