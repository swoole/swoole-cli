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
cd ${__DIR__}


{
  docker stop swoole-cli-build-dev-3
  # docker rm swoole-cli-build-dev-3
} || {
  echo $?
}
cd ${__DIR__}

TAG='all-dependencies-alpine-20230330T153237Z'

IMAGE="phpswoole/swoole-cli-builder:1.6"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"



cd ${__DIR__}
docker run --rm --name swoole-cli-build-dev-3 -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
