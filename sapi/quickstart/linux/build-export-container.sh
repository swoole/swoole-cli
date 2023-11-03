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

container_id='swoole-cli-alpine-dev'
docker cp $container_id:/usr/local/swoole-cli/ .


cat > Dockerfile <<'EOF'
FROM alpine:3.18

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
ENV TZ=Etc/UTC

RUN test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories

RUN apk add ca-certificates tini
ADD ./swoole-cli /usr/local/swoole-cli


RUN uname -m
RUN mkdir /work

WORKDIR /work
ENTRYPOINT ["tini", "--"]

EOF



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
  --platform)
    PLATFORM="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

ARCH=$(uname -m)
TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="1.0.0"
TAG="all-dependencies-alpine-3.18-ffmpeg-opencv-v${VERSION}-${ARCH}-${TIME}"

IMAGE="docker.io/jingjingxyk/build-swoole-cli:${TAG}"
IMAGE="registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:${TAG}"
IMAGE="registry-vpc.cn-beijing.aliyuncs.com/jingjingxyk-public/app:${TAG}"

docker  build -t ${IMAGE} -f ./Dockerfile . --progress=plain  --platform ${PLATFORM}

echo ${IMAGE}
echo ${IMAGE} > container-image.txt

IMAGE_FILE="swoole-cli-builder-ffmpeg-opencv-image.tar"
docker save -o ${IMAGE_FILE} ${IMAGE}

tar -cJvf "${IMAGE_FILE}.xz" ${IMAGE_FILE}


# docker load -i "swoole-cli-builder-ffmpeg-opencv-image.tar"

