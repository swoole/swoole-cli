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

mkdir -p ${__PROJECT__}/var

# export DOCKER_BUILDKIT=1

TIME=$(date -u '+%Y%m%dT%H%M%SZ')
TAG="native-php-all-dependencies-alpine-php-8.2.4-$(uname -m)-${TIME}"

IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"

cd ${__PROJECT__}/var

cp -f ${__DIR__}/Dockerfile-all-dependencies-alpine .

docker build -t ${IMAGE} -f ./Dockerfile-all-dependencies-alpine . --progress=plain

cd ${__PROJECT__}/var

echo ${IMAGE} >swoole-cli-build-all-dependencies-container.txt

docker tag ${IMAGE} ${ALIYUN_IMAGE}

docker push ${ALIYUN_IMAGE}
docker push ${IMAGE}
