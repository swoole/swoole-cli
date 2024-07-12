#!/usr/bin/env bash

mkdir -p /tmp/build

cd /tmp/build/
test -d swoole-src  && rm -rf ./swoole-src


MIRROR=''
DEBUG=0
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --debug)
    DEBUG=1
    ;;
  --*)
    echo "no found mirror option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china )
  git clone -b master --single-branch --depth=1 https://gitee.com/swoole/swoole.git swoole-src
  ;;
*)
  git clone -b master --single-branch --depth=1 https://github.com/swoole/swoole-src.git
  ;;
esac

OPTIONS='';
if [ $DEBUG -eq 1 ] ;then
  OPTIONS='--enable-debug --enable-debug-log --enable-trace-log'
fi


cd swoole-src

phpize
./configure \
--enable-openssl \
--enable-sockets \
--enable-mysqlnd \
--enable-cares \
--enable-swoole-curl \
--enable-swoole-pgsql \
--enable-swoole-sqlite \
--enable-swoole-thread  \
--enable-zts   \
$OPTIONS



make && sudo make install
