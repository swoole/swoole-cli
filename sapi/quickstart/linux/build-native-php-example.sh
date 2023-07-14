#!/bin/env bash
set -uex

: <<'EOF'
# setup container environment

docker run --rm   -ti --init -v .:/work -w /work debian:11
docker run --rm   -ti --init -v .:/work -w /work alpine:3.17

EOF

mkdir -p /tmp/t
cd /tmp/t

PHP_VERSION=8.1.21
PHP_VERSION=8.2.1

test -f php-${PHP_VERSION}.tar.gz || wget -O php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
test -d php-src && rm -rf php-src
mkdir -p php-src
tar --strip-components=1 -C php-src -xf php-${PHP_VERSION}.tar.gz

test -f mongodb-1.16.1.tgz || wget -O mongodb-1.16.1.tgz https://github.com/mongodb/mongo-php-driver/releases/download/1.16.1/mongodb-1.16.1.tgz
mkdir -p mongodb
tar --strip-components=1 -C mongodb -xf mongodb-1.16.1.tgz

test -d php-src/ext/mongodb && rm -rf php-src/ext/mongodb
mv mongodb php-src/ext/

export CC=gcc
export CXX=g++
export LD=ld

export CC=clang
export CXX=clang++
export LD=ld.lld

cd php-src

./buildconf --force

./configure --help
./configure --help | grep 'enable'
./configure --help | grep 'disable'
./configure --help | grep 'with'

./configure \
  --disable-all \
  --disable-cgi \
  --enable-shared=no \
  --enable-static=yes \
  --enable-cli \
  --disable-phpdbg \
  --without-valgrind \
  --enable-mongodb \
  --with-mongodb-system-libs=no \
  --with-mongodb-ssl=no \
  --with-mongodb-sasl=no \
  --with-mongodb-icu=no \
  --with-mongodb-client-side-encryption=no

make -j $(nproc)

file sapi/cli/php
readelf -h sapi/cli/php
