#!/usr/bin/env bash

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

set -uex

PHP_VERSION=8.2.29

test -f php-${PHP_VERSION}.tar.gz || curl -fSLo php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
test -d php-src && rm -rf php-src
mkdir -p php-src
tar --strip-components=1 -C php-src -xf php-${PHP_VERSION}.tar.gz

export CC=clang
export CXX=clang++
export LD=ld.lld

cd php-src

bash ${__DIR__}/opcache-static-compile-patch.sh

./buildconf --force

./configure \
  --disable-all \
  --disable-cgi \
  --enable-shared=no \
  --enable-static=yes \
  --enable-cli \
  --enable-zts \
  --disable-phpdbg \
  --without-valgrind \
  --enable-opcache

export LDFLAGS=" -static -all-static "
sed -i.backup 's/-export-dynamic/-all-static/g' Makefile

make -j $(nproc)

file sapi/cli/php
readelf -h sapi/cli/php

sapi/cli/php -m
sapi/cli/php -v
