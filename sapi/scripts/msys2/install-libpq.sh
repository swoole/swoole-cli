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

VERSION=17.5

download() {
  # document https://github.com/kkos/oniguruma/

  curl -fSLo ${__PROJECT__}/pool/lib/postgresql-${VERSION}.tar.gz https://ftp.postgresql.org/pub/source/v${VERSION}/postgresql-${VERSION}.tar.gz
}

build() {

  cd ${WORK_TEMP_DIR}
  tar xvf ${__PROJECT__}/pool/lib/postgresql-${VERSION}.tar.gz

  cd postgresql-${VERSION}
  ./configure \
    --prefix=/usr \
    --enable-coverage=no \
    --with-ssl=openssl \
    --with-readline \
    --with-icu \
    --without-ldap \
    --with-libxml \
    --with-libxslt \
    --with-lz4 \
    --with-zstd \
    --without-perl \
    --without-python \
    --without-pam \
    --without-ldap \
    --without-bonjour \
    --without-tcl

  make -C src/bin install
  make -C src/include install
  make -C src/interfaces install
  make -C src/common install
  make -C src/port install

}

cd ${__PROJECT__}
test -f ${__PROJECT__}/pool/lib/postgresql-${VERSION}.tar.gz || download

build

: <<'COMMONT'
# 参考文档
# https://www.postgresql.org/docs/current/install-make.html#INSTALL-PROCEDURE-MAKE
# https://www.postgresql.org/docs/16/install-make.html
# https://www.postgresql.org/docs/15/install-procedure.html#CONFIGURE-OPTIONS
# https://www.postgresql.org/docs/
COMMONT
