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

<<<<<<< HEAD
TIME=$(date -u '+%Y%m%dT%H%M%SZ')
TAG="all-dependencies-alpine-swoole-cli-$(uname -m)-${TIME}"

SWOOLE_CLI_IMAGE="docker.io/phpswoole/swoole-cli-builder:1.6"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"

=======
ARCH=$(uname -m)

TAG="1.7-${ARCH}"
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"

USE_COMPOSER_MIRROR=0

while [ $# -gt 0 ]; do
  case "$1" in
  --composer_mirror)
    USE_COMPOSER_MIRROR=1
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


>>>>>>> feature_multistage_build_dependencies_container
cd ${__PROJECT__}/var

cp -f ${__DIR__}/Dockerfile-all-dependencies-alpine .

<<<<<<< HEAD
docker build -t ${IMAGE} -f ./Dockerfile-all-dependencies-alpine . --progress=plain

=======
docker build -t ${IMAGE} -f ./Dockerfile-all-dependencies-alpine . --progress=plain --build-arg USE_COMPOSER_MIRROR=${USE_COMPOSER_MIRROR}
>>>>>>> feature_multistage_build_dependencies_container
cd ${__PROJECT__}/var

echo ${IMAGE} >swoole-cli-build-all-dependencies-container.txt

<<<<<<< HEAD
docker tag ${IMAGE} ${SWOOLE_CLI_IMAGE}
docker tag ${IMAGE} ${ALIYUN_IMAGE}

docker push ${ALIYUN_IMAGE}
docker push ${IMAGE}

docker push ${SWOOLE_CLI_IMAGE}
=======
docker push ${IMAGE}
>>>>>>> feature_multistage_build_dependencies_container
