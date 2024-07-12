#!/usr/bin/env bash
set -x
mkdir -p /tmp/build

# shellcheck disable=SC2164
cd /tmp/build/
test -d swoole-src  && rm -rf ./swoole-src ;


MIRROR=''
DEBUG=0
ENABLE_TEST=0
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --debug)
    DEBUG=1
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

case "$MIRROR" in
china )
  git clone -b master --single-branch --depth=1 https://gitee.com/swoole/swoole.git swoole-src
  ;;
*)
  git clone -b master --single-branch --depth=1 https://github.com/swoole/swoole-src.git
  ;;
esac



cd swoole-src

UNIXODBC_PREFIX='/usr/'
SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,${UNIXODBC_PREFIX}\""
OS=$(uname -s)
ARCH=$(uname -m)
case "$OS-$ARCH" in
Darwin-x86_64)
  export PKG_CONFIG_PATH=/usr/local/opt/libpq/lib/pkgconfig/:/usr/local/opt/unixodbc/lib/pkgconfig/
  UNIXODBC_PREFIX='/usr/local/opt/unixodbc/'
  SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,${UNIXODBC_PREFIX}\""
  ;;
Darwin-arm64)
  export PKG_CONFIG_PATH=/opt/homebrew/opt/libpq/lib/pkgconfig/:/opt/homebrew/opt/unixodbc/lib/pkgconfig/
  UNIXODBC_PREFIX='/opt/homebrew/opt/unixodbc/'
  SWOOLE_ODBC_OPTIONS="--with-swoole-odbc=\"unixODBC,${UNIXODBC_PREFIX}\""
  ;;
Linux-*)
  OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')
  case "$OS_RELEASE" in
    'rocky' | 'almalinux' | 'rhel' |  'centos' | 'fedora' )
    SWOOLE_ODBC_OPTIONS=""
    ;;
  esac
  ;;
*)
  ;;
esac


SWOOLE_DEBUG_OPTIONS='';
if [ $DEBUG -eq 1 ] ;then
  SWOOLE_DEBUG_OPTIONS=' --enable-debug --enable-debug-log --enable-trace-log '
fi


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



# --enable-swoole-thread  \
# --enable-iouring


make  # -j  $(`nproc 2> /dev/null || sysctl -n hw.ncpu`)

test $ENABLE_TEST -eq 1 &&  make test

make install
