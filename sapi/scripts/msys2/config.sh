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
OPTIONS+=' --enable-swoole-thread '
OPTIONS+=' --enable-brotli '
OPTIONS+=' --enable-zstd '
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

WORK_TEMP_DIR=${__PROJECT__}/var/msys2-build/
cd ${WORK_TEMP_DIR}/php-src/

# export CPPFLAGS="-I/usr/include"
# export CFLAGS="-DZEND_WIN32=1 -DPHP_WIN32=1 -DWIN32 "
# https://github.com/php/php-src/blob/php-8.1.27/win32/build/confutils.js#L3227
# export LDFLAGS="-L/usr/lib"

# export CXXFLAGS="-std=gnu++14"
export ICU_CXXFLAGS=" -std=gnu++17 "

./buildconf --force
test -f Makefile && make clean
./configure --help
./configure --prefix=/usr --disable-all \
  \
  --disable-fiber-asm \
  --without-pcre-jit \
  --with-openssl --enable-openssl \
  --with-curl \
  --with-iconv \
  --with-bz2 \
  --enable-bcmath \
  --enable-filter \
  --enable-session \
  --enable-tokenizer \
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
  --enable-xml --enable-simplexml --enable-xmlreader --enable-xmlwriter --enable-dom --with-libxml \
  --enable-swoole --enable-sockets --enable-mysqlnd --enable-swoole-curl --enable-cares \
  --enable-swoole-sqlite \
  --enable-swoole-pgsql \
  --enable-redis \
  --enable-opcache \
  --disable-opcache-jit \
  --with-yaml \
  --with-readline \
  --with-zip \
  --with-sodium \
  --enable-mbstring \
  --with-pgsql \
  --enable-intl \
  ${OPTIONS}

#  --enable-intl \
#  --with-sodium \
#  --enable-swoole-pgsql \
#  --enable-mbstring \ 需要 oniguruma
#  --with-imagick \
#  --enable-gd --with-jpeg --with-freetype \
#  --with-pdo-pgsql \
#  --with-pgsql
#  --with-pdo-sqlite \
#  --with-zip   #  cygwin libzip-devel 版本库暂不支持函数 zip_encryption_method_supported （2020年新增函数)
#  --enable-zts
#  --disable-opcache-jit
