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
mkdir -p bin

WORK_TEMP_DIR=${__PROJECT__}/var/msys2-build/
cd ${__PROJECT__}

mkdir -p bin/.libs

LOGICAL_PROCESSORS=$(nproc)
make -j $LOGICAL_PROCESSORS

${__PROJECT__}/bin/swoole-cli.exe -v
${__PROJECT__}/bin/swoole-cli.exe -m
${__PROJECT__}/bin/swoole-cli.exe --ri swoole
${__PROJECT__}/bin/swoole-cli.exe -v | awk '{print $2}'

APP_VERSION=$(${__PROJECT__}/bin/swoole-cli.exe -v | awk '{print $2}')
APP_NAME='swoole-cli'
echo "v${APP_VERSION}" >${__PROJECT__}/APP_VERSION
echo ${APP_NAME} >${__PROJECT__}/APP_NAME
