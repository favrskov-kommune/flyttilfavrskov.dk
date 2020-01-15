#!/bin/sh

vendor/drush/drush/drush config-set system.file path.temporary /tmp/
vendor/drush/drush/drush cr

if [ $1 = "https" ]; then
	a2enmod ssl
	/etc/init.d/apache2 reload
  (crontab -l -u root; echo "* * * * * wget -O - -q -t 1 https://$DOMAIN/cron/$CRON_HASH >> /var/log/curlcron.log") | crontab
  echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts
  cron
fi

/usr/sbin/apache2ctl -D FOREGROUND
