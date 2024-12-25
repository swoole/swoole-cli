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

if test $LOGICAL_PROCESSORS -gt 2; then
  LOGICAL_PROCESSORS=$((LOGICAL_PROCESSORS - 1))
fi

make -j $LOGICAL_PROCESSORS
./bin/swoole-cli -v
