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

mkdir -p bin/.libs
# export LDFLAGS="-all-static"

LOGICAL_PROCESSORS=$(nproc)

set +u
if [ -n "${GITHUB_ACTION}" ]; then
  make
else
  make -j $LOGICAL_PROCESSORS
fi
set -u

./bin/swoole-cli -v
./bin/swoole-cli -m
./bin/swoole-cli --ri swoole
