#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}

mkdir -p var/build-export-container/
cd ${__PROJECT__}/var/build-export-container/

test -d swoole-cli && rm -rf swoole-cli

IMAGE=registry-vpc.cn-beijing.aliyuncs.com/jingjingxyk-public/app:all-dependencies-alpine-3.18-ffmpeg-opencv-v1.0.0-x86_64-20231103T211807Z
IMAGE=registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:all-dependencies-alpine-3.18-ffmpeg-opencv-v1.0.0-x86_64-20231103T211807Z
container_id=$(docker create $IMAGE) # returns container ID
docker cp $container_id:/usr/local/swoole-cli/ .
docker rm $container_id

container_id='swoole-cli-alpine-dev'
docker cp swoole-cli/ $container_id:/usr/local/


exit 0
IMAGE_FILE="swoole-cli-builder-ffmpeg-opencv-image.tar"
docker save -o ${IMAGE_FILE} ${IMAGE}

tar -cJvf "${IMAGE_FILE}.xz" ${IMAGE_FILE}


# docker load -i "swoole-cli-builder-ffmpeg-opencv-image.tar"

