#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)

cd ${__PROJECT__}

export SWOOLE_CLI_SKIP_DOWNLOAD=1
export SWOOLE_CLI_WITHOUT_DOCKER=1

php prepare.php  --with-build-type=release --skip-download=1 +ds +inotify +apcu

cd ${__PROJECT__}

test -d ${__PROJECT__}/var || mkdir -p ${__PROJECT__}/var

sh sapi/download-dependencies-use-aria2.sh
