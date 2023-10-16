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

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
test -d "${DOWNLOAD_BOX_DIR}" || mkdir -p "${DOWNLOAD_BOX_DIR}"

cp -f ${__DIR__}/Dockerfile-dowload-box "${DOWNLOAD_BOX_DIR}"
cp -f ${__DIR__}/default.conf "${DOWNLOAD_BOX_DIR}"
cp -f ${__PROJECT__}/setup-php-runtime.sh "${DOWNLOAD_BOX_DIR}"

cp -f ${__PROJECT__}/bin/LICENSE "${DOWNLOAD_BOX_DIR}"
cp -f ${__PROJECT__}/bin/credits.html "${DOWNLOAD_BOX_DIR}"
cp -f ${__PROJECT__}/bin/ext-dependency-graph.pdf "${DOWNLOAD_BOX_DIR}"


cd "${DOWNLOAD_BOX_DIR}"


test -f all-archive.zip && rm -rf all-archive.zip

test -d ext && test -d lib && zip -6 -r all-archive.zip ext lib

cd "${DOWNLOAD_BOX_DIR}"

TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="1.7"
TAG="download-box-nginx-alpine-${VERSION}-${TIME}"
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

docker build -t ${IMAGE} -f ./Dockerfile-dowload-box . --progress=plain
echo ${IMAGE} > download-box.txt
# docker push ${IMAGE}
