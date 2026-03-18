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

VERSION=5.2.1

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/giflib-${VERSION}.tar.gz https://sourceforge.net/projects/giflib/files/giflib-${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p giflib-${VERSION}
  tar --strip-components=1 -xvf ${__PROJECT__}/pool/lib/giflib-${VERSION}.tar.gz -C giflib-${VERSION}
  cd giflib-${VERSION}
  make -j $(nproc) libgif.so
  # make install
  cp -f libgif.so /usr/lib/libgif.so
  cp -f gif_lib.h /usr/include/gif_lib.h
}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/giflib-${VERSION}.tar.gz || download

build
