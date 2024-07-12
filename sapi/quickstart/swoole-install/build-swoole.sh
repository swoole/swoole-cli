#!/usr/bin/env bash
set -x
mkdir -p /tmp/build

# shellcheck disable=SC2164
cd /tmp/build/


MIRROR=''
DEBUG=0
ENABLE_TEST=0
VERSION_LATEST=0

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --debug)
    DEBUG=1
    ;;
  --latest)
    VERSION_LATEST=1
    ;;
  --test)
     ENABLE_TEST=1
     ;;
  --*)
    echo "no found mirror option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# 保持源码最新
test $VERSION_LATEST -eq 1 && test -d swoole-src && rm -rf swoole-src

case "$MIRROR" in
china )
  test -d swoole-src || git clone -b master --single-branch --depth=1 https://gitee.com/swoole/swoole.git swoole-src
  ;;
*)
  test -d swoole-src || git clone -b master --single-branch --depth=1 https://github.com/swoole/swoole-src.git
  ;;
esac



SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,/usr\""
SWOOLE_IO_URING=''
SWOOLE_DEBUG_OPTIONS='';
SWOOLE_THREAD_OPTION='';

if [ $DEBUG -eq 1 ] ;then
  SWOOLE_DEBUG_OPTIONS=' --enable-debug --enable-debug-log --enable-trace-log '
fi

# shellcheck disable=SC2046
if [ $(php -r "echo PHP_ZTS;") -eq 1 ] ; then
  SWOOLE_THREAD_OPTION="--enable-swoole-thread"
fi

OS=$(uname -s)
ARCH=$(uname -m)
case "$OS-$ARCH" in
Darwin-x86_64)
  export PKG_CONFIG_PATH=/usr/local/opt/libpq/lib/pkgconfig/:/usr/local/opt/unixodbc/lib/pkgconfig/
  SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,/usr/local/opt/unixodbc/\""
  ;;
Darwin-arm64)
  export PKG_CONFIG_PATH=/opt/homebrew/opt/libpq/lib/pkgconfig/:/opt/homebrew/opt/unixodbc/lib/pkgconfig/
  SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,/opt/homebrew/opt/unixodbc/\""
  ;;
Linux-*)
  OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')
  case "$OS_RELEASE" in
    'rocky' | 'almalinux' | 'rhel' |  'centos' | 'fedora' )
      SWOOLE_ODBC_OPTIONS=""
      ;;
    'debian' | 'ubuntu' | 'alpine' )
      SWOOLE_IO_URING=' --enable-iouring '
      ;;
  esac
  ;;
*)
  ;;
esac




cd swoole-src

phpize

./configure \
${SWOOLE_DEBUG_OPTIONS}  \
--enable-openssl \
--enable-sockets \
--enable-mysqlnd \
--enable-cares \
--enable-swoole-curl \
--enable-swoole-pgsql \
--enable-swoole-sqlite \
${SWOOLE_ODBC_OPTIONS} \
${SWOOLE_IO_URING} \
${SWOOLE_THREAD_OPTION}


# --enable-swoole-thread  \
# --enable-iouring


make  # -j  $(`nproc 2> /dev/null || sysctl -n hw.ncpu`)

test $ENABLE_TEST -eq 1 &&  make test

make install
