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

ROOT=${__PROJECT__}

PHP_VERSION='8.2.13'
SWOOLE_VERSION=v5.1.3

while [ $# -gt 0 ]; do
  case "$1" in
  --php-version)
    PHP_VERSION="$2"
    ;;
  --swoole-version)
    SWOOLE_VERSION="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

REDIS_VERSION=6.0.2
MONGODB_VERSION=1.17.2
YAML_VERSION=2.2.2
IMAGICK_VERSION=3.7.0

if [ ! -d pool/ext ]; then
  mkdir -p pool/ext
fi

test -d ext && rm -rf ext
test -d pool/ext && rm -rf pool/ext
mkdir -p pool/ext
mkdir -p ext
cd pool/ext

if [ ! -d $ROOT/ext/redis ]; then
  if [ ! -f redis-${REDIS_VERSION}.tgz ]; then
    curl -fSLo redis-${REDIS_VERSION}.tgz https://pecl.php.net/get/redis-${REDIS_VERSION}.tgz
  fi
  tar xvf redis-${REDIS_VERSION}.tgz
  mv redis-${REDIS_VERSION} $ROOT/ext/redis
fi

# mongodb no support cygwin
if [ ! -d $ROOT/ext/mongodb ]; then
  if [ ! -f mongodb-${MONGODB_VERSION}.tgz ]; then
    curl -fSLo mongodb-${MONGODB_VERSION}.tgz https://pecl.php.net/get/mongodb-${MONGODB_VERSION}.tgz
  fi
  tar xvf mongodb-${MONGODB_VERSION}.tgz
  mv mongodb-${MONGODB_VERSION} $ROOT/ext/mongodb
fi

if [ ! -d $ROOT/ext/yaml ]; then
  if [ ! -f yaml-${YAML_VERSION}.tgz ]; then
    curl -fSLo yaml-${YAML_VERSION}.tgz https://pecl.php.net/get/yaml-${YAML_VERSION}.tgz
  fi
  tar xvf yaml-${YAML_VERSION}.tgz
  mv yaml-${YAML_VERSION} $ROOT/ext/yaml
fi

if [ ! -d $ROOT/ext/imagick ]; then
  if [ ! -f imagick-${IMAGICK_VERSION}.tgz ]; then
    curl -fSLo imagick-${IMAGICK_VERSION}.tgz https://pecl.php.net/get/imagick-${IMAGICK_VERSION}.tgz
  fi
  tar xvf imagick-${IMAGICK_VERSION}.tgz
  mv imagick-${IMAGICK_VERSION} $ROOT/ext/imagick
fi

if [ ! -f $ROOT/ext/swoole/config.m4 ]; then
  if [ ! -f swoole-${SWOOLE_VERSION}.tgz ]; then
    test -d /tmp/swoole && rm -rf /tmp/swoole
    git clone -b ${SWOOLE_VERSION} https://github.com/swoole/swoole-src.git /tmp/swoole
    cd /tmp/swoole
    tar -czvf $ROOT/pool/ext/swoole-${SWOOLE_VERSION}.tgz .
    cd $ROOT/pool/ext/
  fi
  mkdir -p swoole-${SWOOLE_VERSION}
  tar --strip-components=1 -C swoole-${SWOOLE_VERSION} -xf swoole-${SWOOLE_VERSION}.tgz
  mv swoole-${SWOOLE_VERSION} $ROOT/ext/swoole
fi

cd $ROOT

# download php-src source code

if [ ! -f php-${PHP_VERSION}.tar.gz ]; then
  curl -fSLo php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
fi
test -d php-src && rm -rf php-src
mkdir -p php-src
tar --strip-components=1 -C php-src -xf php-${PHP_VERSION}.tar.gz

cd $ROOT
