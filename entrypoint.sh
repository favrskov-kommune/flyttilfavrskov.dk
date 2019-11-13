#!/bin/sh

vendor/drush/drush/drush config-set system.file path.temporary /tmp/
vendor/drush/drush/drush cr

if [ $1 = "https" ]; then
	a2enmod ssl
	/etc/init.d/apache2 reload
fi

/usr/sbin/apache2ctl -D FOREGROUND
