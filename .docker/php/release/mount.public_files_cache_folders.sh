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
