#!/usr/bin/env bash
echo "[entry point] start"

echo "[entry point] waiting for db to start"
sleep 10 | telnet "$MYSQL_HOST" 3306

echo "[entry point] mysql started"

echo "[entry point] Installing magento"
/usr/local/bin/install-magento

#echo "[entry point] Run OK config"
#composer require okitcom/ok-lib-magento
#
#echo "[entry point] Add cron"
#cat <(crontab -l) <(echo "* * * * * sh /var/www/html/cron.sh") | crontab -

echo "[entry point] done"
exec "$@"
