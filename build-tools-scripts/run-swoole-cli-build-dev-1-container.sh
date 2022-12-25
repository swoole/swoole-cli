#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../
  pwd
)
cd ${__DIR__}


{
  docker stop swoole-cli-build-dev-1
  docker rm swoole-cli-build-dev-1
} || {
  echo $?
}

cd ${__DIR__}

test -f swoole-cli-build-dev-1-container.txt && image=$(cat swoole-cli-build-dev-1-container.txt)
test -f swoole-cli-build-dev-1-container.txt || ( echo '请准备生成构建脚本环境' && exit 0 )

docker run --rm --name swoole-cli-build-dev-1 -d -v ${__PROJECT__}:/work -w /work $image tail -f /dev/null

