#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}


php prepare.php \
--conf-path="./conf.d.extra" \
--with-global-prefix=/usr \
-opcache \
-curl \
-iconv \
-bz2 \
-bcmath \
-pcntl \
-filter \
-session \
-tokenizer \
-mbstring \
-ctype \
-zlib \
-zip \
-posix \
-sockets \
-sqlite3 \
-phar \
-mysqlnd \
-mysqli \
-intl \
-fileinfo \
-pdo_mysql \
-pdo_sqlite \
-soap \
-xsl \
-gmp \
-exif \
-sodium \
-openssl \
-readline \
-xml \
-gd \
-redis \
-swoole \
-yaml \
-imagick \
-mongodb \
--with-swoole-pgsql=1 \
+swoole

