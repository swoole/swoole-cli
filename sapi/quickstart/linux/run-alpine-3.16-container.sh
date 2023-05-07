#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}

{
  docker stop swoole-cli-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}
IMAGE=alpine:3.16

ARCH=$(uname -m)
TAG="native-php-all-dependencies-alpine-php-8.2.4-${ARCH}-20230428T164512Z"
TAG="all-dependencies-alpine-swoole-cli-x86_64-20230505T120137Z"

IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"

#IMAGE=alpine:3.16
cd ${__DIR__}
docker run --rm --name swoole-cli-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
