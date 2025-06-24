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

VERSION=1.0.18

download() {
  # document https://doc.libsodium.org/

  curl -fSLo ${__PROJECT__}/pool/lib/libsodium-${VERSION}.tar.gz https://download.libsodium.org/libsodium/releases/libsodium-${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  tar xvf ${__PROJECT__}/pool/lib/libsodium-${VERSION}.tar.gz

  cd libsodium-${VERSION}

  ./configure --prefix=/usr
  make -j $(nproc)
  make install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/libsodium-${VERSION}.tar.gz || download

build
