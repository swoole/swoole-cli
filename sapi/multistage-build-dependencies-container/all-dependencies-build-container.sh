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

if [[ -f /.dockerenv ]]; then
  echo 'not support in docker'
  exit 0
fi


cd ${__PROJECT__}

mkdir -p ${__PROJECT__}/var

# export DOCKER_BUILDKIT=1

ARCH=$(uname -m)

TIME=$(date -u '+%Y%m%dT%H%M%SZ')

VERSION="1.0.0"
TAG="all-dependencies-alpine-3.17-php7-v${VERSION}-${ARCH}-${TIME}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

COMPOSER_MIRROR=""
MIRROR=""
PLATFORM=''

ARCH=$(uname -m)
case $ARCH in
'x86_64')
  PLATFORM='linux/amd64'
  ;;
'aarch64')
  PLATFORM='linux/arm64'
  ;;
esac

while [ $# -gt 0 ]; do
  case "$1" in
  --composer_mirror)
    COMPOSER_MIRROR="$2"  # "aliyun"  "tencent" "china"
    ;;
  --mirror)
    MIRROR="$2" # "ustc"  "tuna"  "china"
    ;;
  --platform)
    PLATFORM="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

cd ${__PROJECT__}/

cp -f ${__DIR__}/Dockerfile-all-dependencies-alpine .
cp -f ${__DIR__}/php.ini .

docker buildx build -t ${IMAGE} -f ./Dockerfile-all-dependencies-alpine . \
--progress=plain \
--build-arg="COMPOSER_MIRROR=${COMPOSER_MIRROR}" \
--build-arg="MIRROR=${MIRROR}" \
--platform "${PLATFORM}"


cd ${__PROJECT__}/

echo ${IMAGE} > ${__PROJECT__}/var/all-dependencies-container.txt

# docker push ${IMAGE}


# 例子：
# bash build-release-example.sh --mirror china  --build-contianer
# bash sapi/multistage-build-dependencies-container/all-dependencies-build-container.sh --composer_mirror tencent --mirror ustc --platform 'linux/amd64'
# 验证构建结果
# bash sapi/multistage-build-dependencies-container/all-dependencies-run-container-test.sh
