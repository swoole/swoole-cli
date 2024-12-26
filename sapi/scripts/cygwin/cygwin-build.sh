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
  if test $LOGICAL_PROCESSORS -gt 2; then
    LOGICAL_PROCESSORS=$((LOGICAL_PROCESSORS - 1))
  fi
fi
set -u

# make -j $LOGICAL_PROCESSORS
make
./bin/swoole-cli -v
