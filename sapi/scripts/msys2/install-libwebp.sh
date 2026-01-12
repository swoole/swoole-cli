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

VERSION=1.3.2

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/libwebp-${VERSION}.tar.gz https://github.com/webmproject/libwebp/archive/refs/tags/v1.3.2.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libwebp-${VERSION}
  tar --strip-components=1 -xvf ${__PROJECT__}/pool/lib/libwebp-${VERSION}.tar.gz -C libwebp-${VERSION}
  cd libwebp-${VERSION}

  ./autogen.sh
  ./configure \
  --prefix=/usr/ \
  --enable-shared=yes \
  --enable-everything \
   --disable-tiff

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libwebp-${VERSION}.tar.gz || download

build
