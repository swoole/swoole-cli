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

VERSION=068c9f2f643ce59eeb3001d61374bf44a2abd825

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/libyuv-${VERSION}.tar.gz https://chromium.googlesource.com/libyuv/libyuv/+archive/${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libyuv-${VERSION}
  tar xvf ${__PROJECT__}/pool/lib/libyuv-${VERSION}.tar.gz -C libyuv-${VERSION}
  cd libyuv-${VERSION}

  mkdir -p build
  cd build

  cmake -S .. -B . \
    -DCMAKE_INSTALL_PREFIX=/usr/ \
    -DCMAKE_BUILD_TYPE=Release \
    -DCMAKE_SIZEOF_VOID_P=8 \
    -DCMAKE_VERBOSE_MAKEFILE=ON

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libyuv-${VERSION}.tar.gz || download

build
