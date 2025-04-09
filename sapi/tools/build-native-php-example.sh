#!/usr/bin/env bash
set -uex

OS=$(uname -s)
ARCH=$(uname -m)

export CC=clang
export CXX=clang++
export LD=ld.lld

if [ "$OS" = 'Linux' ]; then

  : <<'EOF'
# setup container environment

docker run --rm   -ti --init -v .:/work -w /work debian:11
docker run --rm   -ti --init -v .:/work -w /work alpine:3.17



EOF

fi

if [ "$OS" = 'Darwin' ]; then

  export PATH=/usr/local/opt/bison/bin/:/usr/local/opt/llvm/bin/:$PATH

fi

mkdir -p /tmp/t
cd /tmp/t

PHP_VERSION=8.1.21
PHP_VERSION=8.2.7

test -f php-${PHP_VERSION}.tar.gz || wget -O php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
test -d php-src && rm -rf php-src
mkdir -p php-src
tar --strip-components=1 -C php-src -xf php-${PHP_VERSION}.tar.gz

MONGODB_VERSION=1.16.1
#test -f mongodb-${MONGODB_VERSION}.tgz || wget -O mongodb-${MONGODB_VERSION}.tgz https://github.com/mongodb/mongo-php-driver/releases/download/${MONGODB_VERSION}/mongodb-${MONGODB_VERSION}.tgz
test -f mongodb-${MONGODB_VERSION}.tgz || wget -O mongodb-${MONGODB_VERSION}.tgz https://pecl.php.net/get/mongodb-${MONGODB_VERSION}.tgz
mkdir -p mongodb
tar --strip-components=1 -C mongodb -xf mongodb-${MONGODB_VERSION}.tgz

test -d php-src/ext/mongodb && rm -rf php-src/ext/mongodb
mv mongodb php-src/ext/

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

if [ "$OS" = 'Linux' ]; then

  file sapi/cli/php
  readelf -h sapi/cli/php

else
  otool -L sapi/cli/php
fi
