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

# --- enable ccache to accelerate repeated CI builds (no-op when ccache is missing) ---
if command -v ccache >/dev/null 2>&1; then
    CCACHE_BIN_DIR="${__PROJECT__}/var/ccache-bin"
    CCACHE_DATA_DIR="${__PROJECT__}/var/ccache"
    mkdir -p "${CCACHE_BIN_DIR}" "${CCACHE_DATA_DIR}"
    for CCACHE_TOOL in cc gcc c++ g++; do
        test -e "${CCACHE_BIN_DIR}/${CCACHE_TOOL}" || ln -sf "$(command -v ccache)" "${CCACHE_BIN_DIR}/${CCACHE_TOOL}"
    done
    unset CCACHE_TOOL
    export PATH="${CCACHE_BIN_DIR}:${PATH}"
    export CCACHE_DIR="${CCACHE_DATA_DIR}"
    export CCACHE_MAXSIZE="${CCACHE_MAXSIZE:-2G}"
fi

export ICU_CXXFLAGS=" -std=gnu++17 "
./buildconf --force
test -f Makefile && make clean

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
  --with-swoole-ssh2 \
  --enable-swoole-ftp \
  --enable-redis \
  --with-imagick \
  --with-yaml \
  --with-readline \
  --enable-opcache \
  --disable-opcache-jit
