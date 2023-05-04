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

mkdir -p ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var

test -d swoole-cli || git clone -b main --depth=1 --single-branch https://github.com/swoole/swoole-cli.git
test -d swoole-cli && git -C swoole-cli pull --depth=1

mkdir -p ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var/runtime

if [[ ! -f swoole-cli ]] || [[ ! -f composer.phar ]]; then
  echo "please run ： sh sapi/quickstart/setup-php-runtime.sh "
  exit 0
fi
chmod a+x swoole-cli
chmod a+x composer.phar

cd ${__PROJECT__}/var

cd swoole-cli

mkdir -p pool/lib
mkdir -p pool/ext

sh sapi/download-box/download-box-get-archive-from-container.sh

cd ${__PROJECT__}/var
