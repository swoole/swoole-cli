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
    if test $LOGICAL_PROCESSORS -ge 4; then
      LOGICAL_PROCESSORS=$((LOGICAL_PROCESSORS - 2))
    fi
    make
    # make -j $LOGICAL_PROCESSORS
else
  make -j $LOGICAL_PROCESSORS
fi
set -u

./bin/swoole-cli -v
