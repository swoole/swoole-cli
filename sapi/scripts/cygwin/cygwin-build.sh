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

${__PROJECT__}/php-src/sapi/cli/php -v

cp -f sapi/cli/php.exe  ${__PROJECT__}/bin/
