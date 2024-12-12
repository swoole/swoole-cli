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

OPTIONS=''
X_PHP_VERSION=''
while [ $# -gt 0 ]; do
  case "$1" in
  --php-version)
    PHP_VERSION="$2"
    X_PHP_VERSION=$(echo ${PHP_VERSION:0:3})
    if [ "$X_PHP_VERSION" = "8.4" ]; then
      OPTIONS+=' --enable-swoole-thread '
      OPTIONS+=' --enable-brotli '
      OPTIONS+=' --enable-zstd '
      OPTIONS+=' --enable-zts '
      OPTIONS+=' --disable-opcache-jit '
    fi
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

mkdir -p ${__PROJECT__}/bin/
# cp -f ${__PROJECT__}/php-src/ext/openssl/config0.m4  ${__PROJECT__}/php-src/ext/openssl/config.m4

cp -rf ${__PROJECT__}/ext/* ${__PROJECT__}/php-src/ext/

cd ${__PROJECT__}/php-src/
if [ "$X_PHP_VERSION" = "8.4" ]; then
  sed -i.backup 's/!defined(__HAIKU__)/!defined(__HAIKU__) \&\& !defined(__CYGWIN__)/' TSRM/TSRM.c
fi

# export CPPFLAGS="-I/usr/include"
# export CFLAGS=""
# export LDFLAGS="-L/usr/lib"

./buildconf --force
test -f Makefile && make clean
./configure --prefix=/usr --disable-all \
  \
  --disable-fiber-asm \
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
  --enable-gd --with-jpeg --with-freetype \
  --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
  --enable-swoole-pgsql \
  --enable-swoole-sqlite \
  --enable-redis \
  --enable-opcache \
  --disable-opcache-jit \
  --with-imagick \
  --with-yaml \
  --with-readline \
  ${OPTIONS}

#  --with-pdo-pgsql \
#  --with-pgsql
#  --with-pdo-sqlite \
#  --with-zip   #  cygwin libzip-devel 版本库暂不支持函数 zip_encryption_method_supported （2020年新增函数)
# --enable-zts
# --disable-opcache-jit
