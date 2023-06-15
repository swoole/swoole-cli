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

TAG='download-box-nginx-alpine-20230505T112517Z'

IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"
IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"

cd ${__PROJECT__}/var

container_id=$(docker create $IMAGE) # returns container ID
docker cp $container_id:/usr/share/nginx/html/extensions extensions
docker cp $container_id:/usr/share/nginx/html/libraries libraries

docker rm $container_id

cd ${__PROJECT__}/

mkdir -p pool/lib
mkdir -p pool/ext

awk 'BEGIN { cmd="cp -ri var/libraries/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/extensions/* pool/ext"; print "n" |cmd; }'
