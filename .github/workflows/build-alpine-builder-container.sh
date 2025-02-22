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
cd ${__DIR__}
cd ${__PROJECT__}
mkdir -p var/build-github-action-container/
cd ${__PROJECT__}/var/build-github-action-container/

cp -f ${__PROJECT__}/sapi/quickstart/linux/alpine-init.sh .

cat >Dockerfile <<'EOF'
FROM alpine:3.18

ENV TZ=Etc/UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ADD ./alpine-init.sh /alpine-init.sh

RUN sh /alpine-init.sh

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
'riscv64')
  PLATFORM="linux/riscv64"
  ;;
'mips64le')
  PLATFORM="linux/mips64le"
  ;;
'loongarch64')
  PLATFORM="linux/mips64le"
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

IMAGE='swoole-cli-builder:latest'
docker buildx build -t ${IMAGE} -f ./Dockerfile . --platform ${PLATFORM}

docker save -o "swoole-cli-builder-image.tar" ${IMAGE}

# alpine 可设置的架构选项
# https://hub.docker.com/_/alpine/tags
: <<'EOF'
linux/386
linux/amd64
linux/arm/v6
linux/arm/v7
linux/arm64/v8
linux/ppc64le
linux/s390x
EOF

# 龙芯架构
# https://cr.loongnix.cn/search
