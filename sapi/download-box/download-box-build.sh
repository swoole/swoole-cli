#!/usr/bin/env bash

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

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
test -d "${DOWNLOAD_BOX_DIR}" || mkdir -p "${DOWNLOAD_BOX_DIR}"

cd ${__PROJECT__}/var/download-box/

cp -f ${__DIR__}/Dockerfile-dowload-box .
cp -f ${__DIR__}/default.conf .
cp -f ${__PROJECT__}/setup-php-runtime.sh .

cp -f ${__PROJECT__}/bin/LICENSE .
cp -f ${__PROJECT__}/bin/credits.html .
cp -f ${__PROJECT__}/bin/ext-dependency-graph.pdf .

cd "${DOWNLOAD_BOX_DIR}"

test -f all-deps.zip && rm -rf all-deps.zip

test -d ext && test -d lib && zip -9 -r all-deps.zip ext lib

cd "${DOWNLOAD_BOX_DIR}"

TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="1.8"
TAG="download-box-nginx-alpine-${VERSION}-${TIME}"
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

docker build -t ${IMAGE} -f ./Dockerfile-dowload-box . --progress=plain
echo ${IMAGE} >download-box.txt
docker push ${IMAGE}
