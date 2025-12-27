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

VERSION=2.13.2

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/freetype-${VERSION}.tar.gz https://github.com/freetype/freetype/archive/refs/tags/VER-2-13-2.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p freetype-${VERSION}
  tar --strip-components=1 -xvf ${__PROJECT__}/pool/lib/freetype-${VERSION}.tar.gz -C freetype-${VERSION}
  cd freetype-${VERSION}

  mkdir -p build
  cd build

  cmake -S .. -B . \
  -DCMAKE_INSTALL_PREFIX=/usr \
  -DCMAKE_BUILD_TYPE=Release  \
  -DBUILD_SHARED_LIBS=ON  \
  -DFT_REQUIRE_ZLIB=TRUE \
  -DFT_REQUIRE_BZIP2=TRUE \
  -DFT_REQUIRE_BROTLI=TRUE \
  -DFT_REQUIRE_PNG=TRUE \
  -DFT_DISABLE_HARFBUZZ=TRUE \
  -DCMAKE_POLICY_VERSION_MINIMUM=3.5

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/freetype-${VERSION}.tar.gz || download

build
