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

VERSION=6.9.10

download() {
  # document https://github.com/kkos/oniguruma/

  curl -fSLo ${__PROJECT__}/pool/lib/oniguruma-${VERSION}.tar.gz https://github.com/kkos/oniguruma/archive/refs/tags/v${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  tar xvf ${__PROJECT__}/pool/lib/oniguruma-${VERSION}.tar.gz

  cd oniguruma-${VERSION}
  mkdir -p build
  cd build
  cmake -LH ..
  cmake .. \
    -DCMAKE_INSTALL_PREFIX=/usr \
    -DCMAKE_BUILD_TYPE=Release \
    -DINSTALL_DOCUMENTATION=OFF \
    -DBUILD_TEST=OFF \
    -DCMAKE_VERBOSE_MAKEFILE=ON

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/oniguruma-${VERSION}.tar.gz || download

build
