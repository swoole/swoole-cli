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


TAG='download-box-nginx-alpine-20230329T114730Z'

IMAGE="docker.io/phpswoole/swoole-cli-builder:donload-box-v5.0.2"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
ALIYUN_IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:build-swoole-cli-${TAG}"

cd ${__PROJECT__}/var

test -f download-box.txt && IMAGE=$(head -n 1 download-box.txt)

test -f download-box-aliyun.txt && ALIYUN_IMAGE=$(head -n 1 download-box-aliyun.txt)

echo $IMAGE
echo $ALIYUN_IMAGE

IMAGE=$ALIYUN_IMAGE

cd ${__PROJECT__}/var

container_id=$(docker create $IMAGE) # returns container ID
docker cp $container_id:/usr/share/nginx/html/extensions extensions
docker cp $container_id:/usr/share/nginx/html/libraries libraries

docker rm $container_id

cd ${__PROJECT__}/
mkdir -p ${__PROJECT__}/pool/lib
mkdir -p ${__PROJECT__}/pool/ext

awk 'BEGIN { cmd="cp -ri var/libraries/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/extensions/* pool/ext"; print "n" |cmd; }'
