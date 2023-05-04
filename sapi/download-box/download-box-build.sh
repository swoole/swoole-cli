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
cp -f ${__PROJECT__}/bin/LICENSE ${__PROJECT__}/var
cp -f ${__PROJECT__}/bin/credits.html ${__PROJECT__}/var
cp -f ${__PROJECT__}/bin/ext-dependency-graph.pdf ${__PROJECT__}/var

cd ${__PROJECT__}/var

test -f all-archive.zip && rm -rf all-archive.zip

test -d extensions && test -d libraries && zip -6 -r all-archive.zip extensions libraries

cd ${__PROJECT__}/var

TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="download-box-nginx-alpine-"${TIME}
SWOOLE_CLI_IMAGE="docker.io/phpswoole/swoole-cli-builder:donload-box-v5.0.2"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${VERSION}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${VERSION}"

docker build -t ${IMAGE} -f ./Dockerfile-dowload-box . --progress=plain

docker tag ${IMAGE} ${ALIYUN_IMAGE}
docker tag ${IMAGE} ${SWOOLE_CLI_IMAGE}

echo ${IMAGE} >download-box.txt
echo ${ALIYUN_IMAGE} >download-box-aliyun.txt

docker push ${ALIYUN_IMAGE}
docker push ${IMAGE}
docker push ${SWOOLE_CLI_IMAGE}
