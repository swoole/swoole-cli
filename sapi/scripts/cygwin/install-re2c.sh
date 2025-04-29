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

RE2C_VERSION=3.0

curl -fSLo re2c.exe https://github.com/swoole/swoole-cli/releases/download/v5.0.1/re2c.exe
mv ./re2c.exe /usr/bin/re2c
chmod +x /usr/bin/re2c
re2c -v

build_re2c() {
  cd /tmp
  curl -fSLo re2c-${RE2C_VERSION}.tar.xz https://github.com/skvadrik/re2c/releases/download/3.0/re2c-3.0.tar.xz
  tar xvf re2c-${RE2C_VERSION}.tar.xz
  cd re2c-${RE2C_VERSION}
  autoreconf -i -W all
  ./configure --prefix=/usr && make -j $(nproc) && make install
  cd ${__PROJECT__}
}
