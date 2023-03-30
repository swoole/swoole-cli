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


TAG='download-box-nginx-alpine-20230329T114730Z'

IMAGE="docker.io/phpswoole/swoole-cli-builder:donload-box-v5.0.2"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"


cd ${__PROJECT__}/var

test -f download-box.txt && IMAGE=$(head -n 1 download-box.txt)

test -f download-box-aliyun.txt && ALIYUN_IMAGE=$(head -n 1 download-box-aliyun.txt)

echo $IMAGE
echo $ALIYUN_IMAGE

cd ${__PROJECT__}/
{
  docker stop download-box
  docker rm download-box
} || {
  echo $?
}
docker run -d --rm --name download-box -p 8000:80 ${ALIYUN_IMAGE}
