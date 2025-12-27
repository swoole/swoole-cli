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
mkdir -p pool/lib/
WORK_TEMP_DIR=${__PROJECT__}/var/msys2-build/
mkdir -p ${WORK_TEMP_DIR}

VERSION=7.1.2-8

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/ImageMagick-v${VERSION}.tar.gz https://github.com/ImageMagick/ImageMagick/archive/refs/tags/7.1.2-8.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p ImageMagick-v${VERSION}
  tar --strip-components=1 -xvf ${__PROJECT__}/pool/lib/ImageMagick-v${VERSION}.tar.gz -C ImageMagick-v${VERSION}
  cd ImageMagick-v${VERSION}

  ./configure \
    --prefix=/usr/ \
    --enable-shared=yes \
    --enable-static=no \
    --with-pic \
    --with-zip \
    --with-zlib \
    --with-lzma \
    --with-zstd \
    --with-jpeg \
    --with-png \
    --with-webp \
    --with-xml \
    --with-freetype \
    --with-heic \
    --with-raw \
    --with-tiff \
    --with-lcms \
    --enable-zero-configuration \
    --enable-bounds-checking \
    --enable-hdri \
    --disable-dependency-tracking \
    --without-perl \
    --disable-docs \
    --disable-opencl \
    --disable-openmp \
    --without-djvu \
    --without-rsvg \
    --without-fontconfig \
    --without-jbig \
    --without-jxl \
    --without-openjp2 \
    --without-lqr \
    --without-openexr \
    --without-pango \
    --without-x \
    --without-modules \
    --with-magick-plus-plus \
    --without-utilities \
    --without-gvc \
    --without-autotrace \
    --without-dps \
    --without-fftw \
    --without-flif \
    --without-fpx \
    --without-gslib \
    --without-perl \
    --without-raqm \
    --without-wmf

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/ImageMagick-v${VERSION}.tar.gz || download

build
