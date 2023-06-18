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
  echo 'not in docker'
  exit 0
fi

mkdir -p ${__PROJECT__}/var

# export DOCKER_BUILDKIT=1

ARCH=$(uname -m)

TIME=$(date -u '+%Y%m%dT%H%M%SZ')

VERSION="1.0.0"
TAG="all-dependencies-alpine-3.17-php8-v${VERSION}-${ARCH}-${TIME}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
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

cd ${__PROJECT__}/var

cp -f ${__DIR__}/Dockerfile-all-dependencies-alpine .
cp -f ${__DIR__}/php.ini .

docker build -t ${IMAGE} -f ./Dockerfile-all-dependencies-alpine . --progress=plain --build-arg USE_COMPOSER_MIRROR=${USE_COMPOSER_MIRROR}

cd ${__PROJECT__}/var

echo ${IMAGE} >all-dependencies-container.txt

docker push ${IMAGE}
