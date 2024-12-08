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

test -d ${__PROJECT__}/var/download-box/ || mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/

TAG='download-box-nginx-alpine-1.8-20231113T173944Z'
IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

cd ${__PROJECT__}/var/download-box/

container_id=$(docker create $IMAGE) # returns container ID
docker cp $container_id:/usr/share/nginx/html/ext ext
docker cp $container_id:/usr/share/nginx/html/lib lib

docker rm $container_id

cd ${__PROJECT__}/

mkdir -p pool/lib
mkdir -p pool/ext

awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'
