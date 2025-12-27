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

VERSION=1.6.43

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/libpng-${VERSION}.tar.gz https://sourceforge.net/projects/libpng/files/libpng16/1.6.43/libpng-1.6.43.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libpng-${VERSION}
  tar --strip-components=1 -xvf  ${__PROJECT__}/pool/lib/libpng-${VERSION}.tar.gz -C libpng-${VERSION}
  cd libpng-${VERSION}

  mkdir -p build
  cd build
  cmake -S .. -B . \
  -DCMAKE_INSTALL_PREFIX=/usr \
  -DPNG_SHARED=ON \
  -DPNG_TESTS=OFF

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libpng-${VERSION}.tar.gz || download

build
