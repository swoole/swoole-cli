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
sed -i.bak 's/ICONV_ALIASED_LIBICONV/HAVE_ICONV/' ext/iconv/iconv.c
export PATH=/usr/bin:$PATH

export ICU_CXXFLAGS=" -std=gnu++17 "
./buildconf --force
test -f Makefile && make clean
export AVIF_CFLAGS=$(pkg-config  --cflags --static libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc aom dav1d libheif);
export AVIF_LIBS=$(pkg-config    --libs   --static libbrotlicommon libbrotlidec libbrotlienc SvtAv1Enc aom dav1d libheif);

./configure --prefix=/usr --disable-all \
  --enable-zts \
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
  --with-zip \
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
  --with-imagick \
  --with-yaml \
  --with-readline \
  --enable-opcache \
  --disable-opcache-jit
