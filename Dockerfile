FROM alexcheng/magento2

RUN apt-get update && apt-get install -y \
  mysql-client

#RUN mkdir /var/www/oklib
#COPY . /var/www/oklib
#RUN rm -rf /var/www/oklib/docker /var/www/oklib/.git
#RUN chown -R www-data:www-data /var/www/
#
#COPY docker/bin/* /usr/local/bin/
COPY --chown=www-data:www-data app/ /var/www/html/app/code/Okitcom/OkLibMagento/

COPY entrypoint_install.sh /sbin/entrypoint_install.sh
RUN chmod +x /sbin/entrypoint_install.sh

ENV TZ=Europe/Amsterdam
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ENTRYPOINT ["/sbin/entrypoint_install.sh"]
CMD ["/sbin/my_init"]
