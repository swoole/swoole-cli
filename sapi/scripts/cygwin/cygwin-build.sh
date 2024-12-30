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

LOGICAL_PROCESSORS=$(nproc)

set +u
if [ -n "${GITHUB_ACTION}" ]; then
  if test $LOGICAL_PROCESSORS -ge 4; then
    LOGICAL_PROCESSORS=$((LOGICAL_PROCESSORS - 2))
  fi
  make cli
  # make -j $LOGICAL_PROCESSORS
else
  make -j $LOGICAL_PROCESSORS cli
fi
set -u

${__PROJECT__}/php-src/sapi/cli/php.exe -v

cp -f ${__PROJECT__}/php-src/sapi/cli/php.exe ${__PROJECT__}/bin/

${__PROJECT__}/bin/php.exe -v
${__PROJECT__}/bin/php.exe -m
${__PROJECT__}/bin/php.exe --ri swoole
