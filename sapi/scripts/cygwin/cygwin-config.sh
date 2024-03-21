#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}



./buildconf --force
test -f Makefile && make clean
./configure --prefix=/usr --disable-all \
    --disable-fiber-asm \
    --enable-opcache \
    --without-pcre-jit \
    --with-openssl --enable-openssl \
    --with-curl \
    --with-iconv \
    --enable-intl \
    --with-bz2 \
    --enable-bcmath \
    --enable-filter \
    --enable-session \
    --enable-tokenizer \
    --enable-mbstring \
    --enable-ctype \
    --with-zlib \
    --with-zip \
    --enable-posix \
    --enable-sockets \
    --enable-pdo \
    --with-sqlite3 \
    --enable-phar \
    --enable-pcntl \
    --enable-mysqlnd \
    --with-mysqli \
    --enable-fileinfo \
    --with-pdo_mysql \
    --enable-soap \
    --with-xsl \
    --with-gmp \
    --enable-exif \
    --with-sodium \
    --enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
    --enable-gd --with-jpeg  --with-freetype \
    --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
    --enable-swoole-pgsql \
    --enable-swoole-sqlite \
    --enable-redis \
    --with-imagick \
    --with-yaml \
    --with-readline


#    --with-pdo-sqlite \
