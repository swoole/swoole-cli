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
cd ${__PROJECT__}/thirdparty/php_src

mkdir -p bin/.libs

make -j $(nproc) cli

${__PROJECT__}/thirdparty/php_src/sapi/cli/php -v

cp -f sapi/cli/php.exe  ${__PROJECT__}/bin/
