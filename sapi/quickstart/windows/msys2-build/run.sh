#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}
pwd

bash sapi/scripts/msys2/config-ext.sh
bash sapi/scripts/msys2/config.sh
bash sapi/scripts/msys2/build.sh
bash sapi/scripts/msys2/archive.sh
