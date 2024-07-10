#!/bin/bash

:<<'COMMENT'
  从运行中的容器 将 /usr/local/swoole-cli/ 文件夹 拷贝出来 并生成新容器镜像 和 导出镜像到磁盘

COMMENT

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}


CONTAINER_BASE_IMAGE='docker.io/library/alpine:3.18'
MIRROR=''
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
  --mirror)
    MIRROR="$2"
      ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
  china | openatom)
    CONTAINER_BASE_IMAGE="hub.atomgit.com/library/alpine:3.18"
    ;;
esac


mkdir -p var/build-export-container/
cd ${__PROJECT__}/var/build-export-container/

test -d swoole-cli && rm -rf swoole-cli

container_id='swoole-cli-builder'
docker cp $container_id:/usr/local/swoole-cli/ .


cat > Dockerfile <<'EOF'
ARG BASE_IMAGE="alpine:3.18"
FROM ${BASE_IMAGE}
# FROM alpine:3.18

ARG MIRROR=""

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
ENV TZ=Etc/UTC

RUN test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
RUN if [ "${MIRROR}" = "ustc" -o "${MIRROR}" = "china"   ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories ; } fi
RUN if [ "${MIRROR}" = "tuna" ]; then { sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories ; } fi

RUN apk add ca-certificates tini
ADD ./swoole-cli /usr/local/swoole-cli

RUN cp -f /etc/apk/repositories.save /etc/apk/repositories
RUN uname -m
RUN mkdir /work

WORKDIR /work
ENTRYPOINT ["tini", "--"]

EOF



ARCH=$(uname -m)
TIME=$(date -u '+%Y%m%dT%H%M%SZ')
VERSION="1.6"
TAG="v${VERSION}-${ARCH}-${TIME}"

IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"

echo "MIRROR=${MIRROR}"
echo "BASE_IMAGE=${CONTAINER_BASE_IMAGE}"
docker  build -t ${IMAGE} -f ./Dockerfile . --progress=plain  --platform ${PLATFORM} --build-arg="MIRROR=${MIRROR}" --build-arg="BASE_IMAGE=${CONTAINER_BASE_IMAGE}"

echo ${IMAGE}
echo ${IMAGE} > container-image.txt

IMAGE_FILE="swoole-cli-builder.tar"
docker save -o ${IMAGE_FILE} ${IMAGE}

tar -cJvf "${IMAGE_FILE}.xz" ${IMAGE_FILE}


# docker load -i "swoole-cli-builder-image.tar"

