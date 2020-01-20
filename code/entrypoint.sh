#!/bin/sh

if [ $1 = "https" ]; then
	a2enmod expires
  (crontab -l -u root; echo "*/5 * * * * wget -O - -q -t 1 https://$DOMAIN/cron/$CRON_HASH >> /var/log/curlcron.log") | crontab
  echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts
  cron
fi

/usr/sbin/apache2ctl -D FOREGROUND
