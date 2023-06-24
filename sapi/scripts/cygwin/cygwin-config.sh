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
VERSION=8.1.20

test -f php-${VERSION}.tar.gz ||  wget -O php-${VERSION}.tar.gz  https://github.com/php/php-src/archive/refs/tags/php-${VERSION}.tar.gz

mkdir -p php-src
tar --strip-components=1 -C php-src -xf  php-${VERSION}.tar.gz

mkdir -p ${__PROJECT__}/bin/
# cp -f ${__PROJECT__}/php-src/ext/openssl/config0.m4  ${__PROJECT__}/php-src/ext/openssl/config.m4

cp -rf ${__PROJECT__}/ext/* ${__PROJECT__}/php-src/ext/

cd ${__PROJECT__}/php-src/

# export CPPFLAGS="-I/usr/include"
# export CFLAGS=""
# export LDFLAGS="-L/usr/lib"



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
    --with-pdo-sqlite \
    --enable-soap \
    --with-xsl \
    --with-gmp \
    --enable-exif \
    --with-sodium \
    --enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
    --enable-gd --with-jpeg  --with-freetype \
    --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
    --enable-redis \
    --with-imagick \
    --with-yaml \
    --with-readline \
    --with-pdo-pgsql \
    --with-pgsql
