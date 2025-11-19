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

OPTIONS+=' --enable-zts '
OPTIONS+=' --disable-opcache-jit '

X_PHP_VERSION=''
while [ $# -gt 0 ]; do
  case "$1" in
  --php-version)
    PHP_VERSION="$2"
    X_PHP_VERSION=$(echo ${PHP_VERSION:0:3})
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

mkdir -p ${__PROJECT__}/bin/

WORK_TEMP_DIR=${__PROJECT__}/var/cygwin-build/
cd ${WORK_TEMP_DIR}/php-src/

# export CPPFLAGS="-I/usr/include"
# export CFLAGS=""
# export LDFLAGS="-L/usr/lib"

sed -i.bak 's/ICONV_ALIASED_LIBICONV/HAVE_ICONV/' ext/iconv/iconv.c
export PATH=/usr/bin:$PATH

export ICU_CXXFLAGS=" -std=gnu++17 "
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
  --enable-gd --with-jpeg --with-freetype --with-avif --with-webp \
  --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
  --enable-swoole-pgsql \
  --enable-swoole-sqlite \
  --enable-swoole-thread \
  --enable-brotli \
  --enable-zstd \
  --enable-swoole-stdext \
  --enable-redis \
  --enable-opcache \
  --disable-opcache-jit \
  --with-imagick \
  --with-yaml \
  --with-readline \
  --with-zip \
  --with-pgsql \
  ${OPTIONS}

#  --with-pdo-pgsql \
#  --with-pgsql
#  --with-pdo-sqlite \
#  --with-zip   #  cygwin libzip-devel 版本库暂不支持函数 zip_encryption_method_supported （2020年新增函数)
# --enable-zts
# --disable-opcache-jit
