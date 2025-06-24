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
WORK_TEMP_DIR=${__PROJECT__}/var/cygwin-build/
cd ${WORK_TEMP_DIR}/php-src/

mkdir -p bin/.libs
# export LDFLAGS="-all-static"

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

${WORK_TEMP_DIR}/php-src/sapi/cli/php.exe -v

cp -f ${WORK_TEMP_DIR}/php-src/sapi/cli/php.exe ${__PROJECT__}/bin/

${__PROJECT__}/bin/php.exe -v
${__PROJECT__}/bin/php.exe -m
${__PROJECT__}/bin/php.exe --ri swoole

cd ${__PROJECT__}
APP_VERSION=$(${__PROJECT__}/bin/php.exe -v | awk '{print $2}')
APP_NAME='php-cli'
echo "v${APP_VERSION}" >${__PROJECT__}/APP_VERSION
echo ${APP_NAME} >${__PROJECT__}/APP_NAME
