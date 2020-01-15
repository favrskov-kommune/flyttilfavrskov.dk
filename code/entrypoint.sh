#!/bin/sh
vendor/drush/drush/drush config-set system.file path.temporary /tmp/
vendor/drush/drush/drush updb -y
vendor/drush/drush/drush cim -y
vendor/drush/drush/drush locale-check -y
vendor/drush/drush/drush locale-update -y

echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts
a2enmod ssl
/etc/init.d/apache2 reload
/usr/sbin/apache2ctl -D FOREGROUND
