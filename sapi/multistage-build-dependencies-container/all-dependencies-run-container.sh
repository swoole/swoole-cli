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

cd ${__PROJECT__}/
ARCH=$(uname -m)

TAG="native-php-all-dependencies-alpine-php-8.2.4-${ARCH}-20230428T164512Z"

IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"

docker run --rm --name swoole-cli-all-dependencies-container -d -v ${__PROJECT__}:/work -w /work -ti --init ${IMAGE}
