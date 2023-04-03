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

cd ${__PROJECT__}/sapi/docker/

mkdir -p ${__PROJECT__}/var/



# export DOCKER_BUILDKIT=1

TIME=`date -u '+%Y%m%dT%H%M%SZ'`
TAG="all-dependencies-alpine-arm64-${TIME}"

IMAGE="docker.io/phpswoole/swoole-cli-builder:1.6"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"


cd ${__PROJECT__}/var


cp -f ${__PROJECT__}/sapi/docker/Dockerfile-arm64 ${__PROJECT__}/var/

sed -i "s/--allow-untrusted//g" Dockerfile-arm64
sed -i "s/RUN apk upgrade //g" Dockerfile-arm64
sed -i "s@arm64v8/alpine:latest@multiarch/alpine:arm64-latest-stable@g" Dockerfile-arm64
sed -i "s@mirrors.tuna.tsinghua.edu.cn@mirrors.ustc.edu.cn@g" Dockerfile-arm64


docker build -t ${IMAGE} -f ./Dockerfile-arm64  . --progress=plain




cd ${__PROJECT__}/var

echo ${IMAGE} > swoole-cli-build-all-dependencies-arm64-container.txt


exit 0
docker tag ${IMAGE} ${ALIYUN_IMAGE}

docker push ${ALIYUN_IMAGE}
docker push ${IMAGE}




