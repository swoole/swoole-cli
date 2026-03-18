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

VERSION=1.3.0

download() {
  # document https://github.com/AOMediaCodec/libavif/
  curl -fSLo ${__PROJECT__}/pool/lib/libavif-v${VERSION}.tar.gz https://github.com/AOMediaCodec/libavif/archive/refs/tags/v${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  tar xvf ${__PROJECT__}/pool/lib/libavif-v${VERSION}.tar.gz
  cd libavif-${VERSION}

  mkdir -p build
  cd build
  cmake .. \
    -DCMAKE_INSTALL_PREFIX=/usr \
    -DCMAKE_BUILD_TYPE=Release \
    -DBUILD_TOOLS=OFF \
    -DBUILD_EXAMPLES=OFF \
    -DBUILD_DOC=OFF \
    -DLIBZIP_DO_INSTALL=ON \
    -DENABLE_GNUTLS=OFF \
    -DENABLE_MBEDTLS=OFF \
    -DENABLE_OPENSSL=ON \
    -DENABLE_BZIP2=ON \
    -DENABLE_COMMONCRYPTO=OFF \
    -DENABLE_LZMA=ON \
    -DENABLE_ZSTD=ON \
    -DBUILD_REGRESS=OFF \
    -DBUILD_OSSFUZZ=OFF \
    -DCMAKE_VERBOSE_MAKEFILE=ON


  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libavif-v${VERSION}.tar.gz || download

build
