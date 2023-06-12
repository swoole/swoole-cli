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

test -d ${__PROJECT__}/var || mkdir -p ${__PROJECT__}/var

cp -f ${__DIR__}/Dockerfile-dowload-box ${__PROJECT__}/var
cp -f ${__DIR__}/default.conf ${__PROJECT__}/var
cp -f ${__PROJECT__}/setup-php-runtime.sh ${__PROJECT__}/var

cp -f ${__PROJECT__}/bin/LICENSE ${__PROJECT__}/var
cp -f ${__PROJECT__}/bin/credits.html ${__PROJECT__}/var
cp -f ${__PROJECT__}/bin/ext-dependency-graph.pdf ${__PROJECT__}/var


cd ${__PROJECT__}/var

test -f all-archive.zip && rm -rf all-archive.zip

test -d extensions && test -d libraries && zip -6 -r all-archive.zip extensions libraries

cd ${__PROJECT__}/var

TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="1.7"
TAG="download-box-nginx-alpine-${VERSION}-${TIME}"
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

docker build -t ${IMAGE} -f ./Dockerfile-dowload-box . --progress=plain
echo ${IMAGE} >download-box.txt
docker push ${IMAGE}
