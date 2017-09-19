FROM php:7.0-fpm
MAINTAINER Hidde Lycklama <hidde@okit.com>

RUN apt-get update && apt-get install -y \
  cron \
  libfreetype6-dev \
  libicu-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libpng12-dev \
  libxslt1-dev \
  mysql-client \
  zip \
  git

RUN docker-php-ext-configure \
  gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/

RUN docker-php-ext-install \
  bcmath \
  gd \
  intl \
  mbstring \
  mcrypt \
  opcache \
  pdo_mysql \
  soap \
  xsl \
  zip

RUN curl -sS https://getcomposer.org/installer | \
  php -- --install-dir=/usr/local/bin --filename=composer

ENV PHP_MEMORY_LIMIT 2G
ENV PHP_PORT 9000
ENV PHP_PM dynamic
ENV PHP_PM_MAX_CHILDREN 10
ENV PHP_PM_START_SERVERS 4
ENV PHP_PM_MIN_SPARE_SERVERS 2
ENV PHP_PM_MAX_SPARE_SERVERS 6
ENV APP_MAGE_MODE default

COPY docker/conf/www.conf /usr/local/etc/php-fpm.d/
COPY docker/conf/php.ini /usr/local/etc/php/
COPY docker/conf/php-fpm.conf /usr/local/etc/

RUN mkdir /var/www/oklib
COPY . /var/www/oklib
RUN rm -rf /var/www/oklib/docker /var/www/oklib/.git
RUN chown -R www-data:www-data /var/www/

COPY docker/bin/* /usr/local/bin/

WORKDIR /var/www/html

CMD ["/usr/local/bin/start"]
