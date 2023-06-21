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

# --with-swoole-pgsql=1
# --with-php-version=8.1.20
# --with-global-prefix=/usr \
# @macos
# --conf-path="./conf.d.extra"

php prepare.php \
--conf-path="./conf.d.extra" \
--with-global-prefix=/usr/local/swoole-cli \
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
-pgsql -pdo_pgsql \
--with-swoole-pgsql=1 \
+swoole \
--with-php-version=8.1.18 \



