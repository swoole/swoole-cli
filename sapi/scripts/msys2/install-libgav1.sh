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

VERSION=v0.19.0

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/libgav1-${VERSION}.tar.gz https://chromium.googlesource.com/codecs/libgav1/+archive/e386d8f1fb983200972d159b9be47fd5d0776708.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libgav1-${VERSION}
  tar -xvf ${__PROJECT__}/pool/lib/libgav1-${VERSION}.tar.gz -C libgav1-${VERSION}
  cd libgav1-${VERSION}

  mkdir -p build
  cd build
  cmake -G "Unix Makefiles" -S .. -B . \
    -DCMAKE_INSTALL_PREFIX=/usr/ \
    -DCMAKE_BUILD_TYPE=Release \
    -DBUILD_SHARED_LIBS=ON \
    -DBUILD_STATIC_LIBS=OFF \
    -DLIBGAV1_ENABLE_TESTS=OFF \
    -DLIBGAV1_ENABLE_EXAMPLES=OFF \
    -DLIBGAV1_THREADPOOL_USE_STD_MUTEX=1

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libgav1-${VERSION}.tar.gz || download

build
