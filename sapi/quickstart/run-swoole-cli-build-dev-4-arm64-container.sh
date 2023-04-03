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
  docker stop swoole-cli-build-dev-4
  # docker rm swoole-cli-build-dev-4
} || {
  echo $?
}
cd ${__DIR__}
IMAGE=docker.io/jingjingxyk/build-swoole-cli:all-dependencies-alpine-arm64-20230403T073941Z
IMAGE=docker.io/jingjingxyk/build-swoole-cli:alpine-arm64-20230403T073941Z


cd ${__DIR__}
docker run --rm --name swoole-cli-build-dev-4 -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
