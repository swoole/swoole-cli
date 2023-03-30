#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)

mkdir -p ${__PROJECT__}/var
cd ${__PROJECT__}/var


test -d swoole-cli || git clone -b main --depth=1 --single-branch  https://github.com/swoole/swoole-cli.git
test -d swoole-cli &&  git -C swoole-cli  pull --depth=1

mkdir -p  ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var/runtime
test -f swoole-cli-v5.0.2-linux-x64.tar.xz || wget -O swoole-cli-v5.0.2-linux-x64.tar.xz  https://github.com/swoole/swoole-src/releases/download/v5.0.2/swoole-cli-v5.0.2-linux-x64.tar.xz
test -f swoole-cli-v5.0.2-linux-x64.tar ||  xz -d -k swoole-cli-v5.0.2-linux-x64.tar.xz
test -f swoole-cli ||  tar -xvf swoole-cli-v5.0.2-linux-x64.tar
chmod a+x swoole-cli

test -f composer.phar ||  wget -O composer.phar https://getcomposer.org/download/2.5.5/composer.phar


cd ${__PROJECT__}/var

cd swoole-cli
mkdir -p pool/lib
mkdir -p pool/ext
sh sapi/download-box/download-box-get-archive-from-container.sh

cd ${__PROJECT__}/var

