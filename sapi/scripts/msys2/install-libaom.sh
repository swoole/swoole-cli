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

VERSION=v3.10.0

download() {
  curl -fSLo ${__PROJECT__}/pool/lib/libaom-${VERSION}.tar.gz https://aomedia.googlesource.com/aom/+archive/c2fe6bf370f7c14fbaf12884b76244a3cfd7c5fc.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  mkdir -p libaom-${VERSION}
  tar -xvf ${__PROJECT__}/pool/lib/libaom-${VERSION}.tar.gz -C libaom-${VERSION}
  cd libaom-${VERSION}

  mkdir -p build_dir
  cd build_dir
   cmake -S .. -B . \
  -DCMAKE_INSTALL_PREFIX=/usr/ \
  -DCMAKE_BUILD_TYPE=Release  \
  -DCMAKE_C_STANDARD=11 \
  -DBUILD_SHARED_LIBS=OFF  \
  -DBUILD_STATIC_LIBS=ON \
  -DENABLE_DOCS=OFF \
  -DENABLE_EXAMPLES=OFF \
  -DENABLE_TESTS=OFF \
  -DENABLE_TOOLS=ON

  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libaom-${VERSION}.tar.gz || download

build
