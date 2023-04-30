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


make -j $(nproc) cli


cd ${__PROJECT__}
mkdir -p bin/.libs

make -j $(nproc)

${__PROJECT__}/php-src/sapi/cli/php-cli.exe -v

cp -f ${__PROJECT__}/bin/${VERSION}/php.exe  ${__PROJECT__}/bin/
cp -f ${__PROJECT__}/bin/${VERSION}/php.exe  ${__PROJECT__}/bin/php-cli.exe
${__PROJECT__}/bin/php-cli.exe -v
