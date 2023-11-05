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

container_id='swoole-cli-builder'
docker cp $container_id:/usr/local/swoole-cli/ .


cat > Dockerfile <<'EOF'
FROM alpine:edge

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
ENV TZ=Etc/UTC

RUN test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories

RUN apk add ca-certificates tini
ADD ./swoole-cli /usr/local/swoole-cli

RUN cp -f /etc/apk/repositories.save /etc/apk/repositories
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
VERSION="1.6"
TAG="v${VERSION}-${ARCH}-${TIME}"

IMAGE="docker.io/phpswoole/swoole-cli-builder:${TAG}"

docker  build -t ${IMAGE} -f ./Dockerfile . --progress=plain  --platform ${PLATFORM}

echo ${IMAGE}
echo ${IMAGE} > container-image.txt

IMAGE_FILE="swoole-cli-builder.tar"
docker save -o ${IMAGE_FILE} ${IMAGE}

tar -cJvf "${IMAGE_FILE}.xz" ${IMAGE_FILE}


# docker load -i "swoole-cli-builder-image.tar"

