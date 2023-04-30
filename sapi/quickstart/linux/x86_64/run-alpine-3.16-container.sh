#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
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
IMAGE=docker.io/jingjingxyk/build-swoole-cli:native-php-all-dependencies-alpine-20230428T164512Z
IMAGE=docker.io/jingjingxyk/build-swoole-cli:native-php-all-dependencies-alpine-20230429T064207Z

cd ${__DIR__}
docker run --rm --name swoole-cli-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
