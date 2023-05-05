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

TAG="1.7-${ARCH}"
TAG="all-dependencies-alpine-swoole-cli-x86_64-20230505T120137Z"
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"

docker run --rm --name swoole-cli-all-dependencies-container -d -v ${__PROJECT__}:/work -w /work -ti --init ${IMAGE}
