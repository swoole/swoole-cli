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

VERSION=3.1.2

download() {
  # document https://github.com/libjpeg-turbo/libjpeg-turbo/
  curl -fSLo ${__PROJECT__}/pool/lib/libjpeg-${VERSION}.tar.gz https://github.com/libjpeg-turbo/libjpeg-turbo/archive/refs/tags/${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libjpeg-${VERSION}
  tar --strip-components=1 -xvf ${__PROJECT__}/pool/lib/libjpeg-${VERSION}.tar.gz -C libjpeg-${VERSION}
  cd libjpeg-${VERSION}

  mkdir -p build
  cd build

  cmake -G"Unix Makefiles"   -S .. -B . \
  -DCMAKE_INSTALL_PREFIX=/usr/ \
  -DCMAKE_INSTALL_LIBDIR=/usr/lib \
  -DCMAKE_INSTALL_INCLUDEDIR=/usr/include \
  -DCMAKE_BUILD_TYPE=Release  \
  -DBUILD_SHARED_LIBS=ON  \
  -DBUILD_STATIC_LIBS=OFF


  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libjpeg-${VERSION}.tar.gz || download

build
