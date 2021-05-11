#!/bin/bash

# Mount local cache files out of webroot
if [[ ! -d /mnt/cache/php ]]; then
    mkdir /mnt/cache/php
fi
rm -rf /app/webroot/sites/beierholm.dk/files/php/*
mount -o bind /mnt/cache/php /app/webroot/sites/beierholm.dk/files/php

if [[ ! -d /mnt/cache/js ]]; then
    mkdir /mnt/cache/js
fi
rm -f /app/webroot/sites/beierholm.dk/files/js/optimized/.htaccess
rm -rf /app/webroot/sites/beierholm.dk/files/js/*
mount -o bind /mnt/cache/js /app/webroot/sites/beierholm.dk/files/js

if [[ ! -d /mnt/cache/css ]]; then
    mkdir /mnt/cache/css
fi
rm -f /app/webroot/sites/beierholm.dk/files/css/optimized/.htaccess
rm -rf /app/webroot/sites/beierholm.dk/files/css/*
mount -o bind /mnt/cache/css /app/webroot/sites/beierholm.dk/files/css

if [[ ! -d /mnt/cache/styles ]]; then
    mkdir /mnt/cache/styles
fi
rm -rf /app/webroot/sites/beierholm.dk/files/styles/*
mount -o bind /mnt/cache/styles /app/webroot/sites/beierholm.dk/files/styles

echo "Cache mounts complete."

# Mount FTP dir
mount -o bind /mnt/ftp/beierholm /app/private-files/ftp

echo "FTP mounts complete."

# Mount logs dir
if [[ ! -d /app/logs ]]; then
    mkdir /app/logs
fi

rm -rf /app/logs/*  /app/logs/.keep
mount -o bind /mnt/logs /app/logs

echo "Logs mounts complete."
