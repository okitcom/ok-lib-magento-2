FROM alexcheng/magento2

RUN apt-get update && apt-get install -y \
  telnet

COPY --chown=www-data:www-data app/ /var/www/html/app/code/Okitcom/OkLibMagento/
COPY --chown=www-data:www-data lib/ /var/www/html/app/code/OK

COPY entrypoint_install.sh /sbin/entrypoint_install.sh
RUN chmod +x /sbin/entrypoint_install.sh

ENV TZ=Europe/Amsterdam
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ENTRYPOINT ["/sbin/entrypoint_install.sh"]
CMD ["/sbin/my_init"]
