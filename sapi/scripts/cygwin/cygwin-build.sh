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
cd ${__PROJECT__}/php-src

mkdir -p bin/.libs

make -j $(nproc) cli

${__PROJECT__}/php-src/sapi/cli/php.exe -v

cp -f ${__PROJECT__}/php-src/sapi/cli/php.exe ${__PROJECT__}/bin/
cp -f ${__PROJECT__}/php-src/sapi/cli/php.exe ${__PROJECT__}/bin/php-cli.exe

${__PROJECT__}/bin/php-cli.exe -v
${__PROJECT__}/bin/php-cli.exe -m
${__PROJECT__}/bin/php-cli.exe --ri swoole
