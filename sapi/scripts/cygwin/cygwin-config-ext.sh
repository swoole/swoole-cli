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

PHP_VERSION=$(cat ${__PROJECT__}/sapi/PHP-VERSION.conf)
SWOOLE_VERSION=v4.8.13

while [ $# -gt 0 ]; do
  case "$1" in
  --swoole-version)
    SWOOLE_VERSION="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

REDIS_VERSION=5.3.7
MONGODB_VERSION=1.14.2
YAML_VERSION=2.2.2
IMAGICK_VERSION=3.7.0

if [ ! -d pool/ext ]; then
  mkdir -p pool/ext
fi

cd pool/ext

if [ ! -d $ROOT/ext/redis ]; then
  if [ ! -f redis-${REDIS_VERSION}.tgz ]; then
    curl -fSLo redis-${REDIS_VERSION}.tgz https://pecl.php.net/get/redis-${REDIS_VERSION}.tgz
  fi
  tar xvf redis-${REDIS_VERSION}.tgz
  mv redis-${REDIS_VERSION} $ROOT/ext/redis
fi

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

if [ ! -f swoole-${SWOOLE_VERSION}.tgz ]; then
  test -d /tmp/swoole && rm -rf /tmp/swoole
  git clone -b ${SWOOLE_VERSION} https://github.com/swoole/swoole-src.git /tmp/swoole
  cd /tmp/swoole
  tar -czvf $ROOT/pool/ext/swoole-${SWOOLE_VERSION}.tgz .
  cd $ROOT/pool/ext/
fi
mkdir -p swoole-${SWOOLE_VERSION}
tar --strip-components=1 -C swoole-${SWOOLE_VERSION} -xf swoole-${SWOOLE_VERSION}.tgz
test -d $ROOT/ext/swoole && rm -rf $ROOT/ext/swoole
mv swoole-${SWOOLE_VERSION} $ROOT/ext/swoole

cd $ROOT
# downgload php-src source code

if [ ! -f php-${PHP_VERSION}.tar.gz ]; then
  curl -fSLo php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
fi
test -d php-src && rm -rf php-src
mkdir -p php-src
tar --strip-components=1 -C php-src -xf php-${PHP_VERSION}.tar.gz

cd $ROOT

if [ ! -d $ROOT/ext/pgsql ]; then
  mv $ROOT/php-src/ext/pgsql $ROOT/ext/pgsql
fi

cd $ROOT

# cp -f $ROOT/php-src/Zend/zend_vm_gen.php $ROOT/Zend/
ls -lha $ROOT/Zend/zend_vm_gen.php
ls -lh $ROOT
ls -lh $ROOT/ext/
cd $ROOT
