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

cp -f ${__PROJECT__}/sapi/quickstart/linux/debian-init.sh .
cp -f ${__PROJECT__}/sapi/quickstart/linux/extra/debian-php-init.sh .

cat > Dockerfile <<'EOF'
ARG BASE_IMAGE=debian:12
FROM ${BASE_IMAGE}
# FROM debian:unstable

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Etc/UTC
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

ADD ./debian-init.sh /debian-init.sh

RUN bash /debian-init.sh
# RUN sh /debian-init.sh --mirror china

ADD ./debian-php-init.sh /debian-php-init.sh
RUN bash /debian-php-init.sh

RUN uname -m
RUN mkdir /work

WORKDIR /work
ENTRYPOINT ["tini", "--"]

EOF



PLATFORM='linux/amd64'
BASE_IMAGE="debian:12"
while [ $# -gt 0 ]; do
  case "$1" in
  --platform)
    PLATFORM="$2"
    ;;
  --container-image)
    BASE_IMAGE="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done



IMAGE='swoole-cli-builder:latest'
docker buildx build -t ${IMAGE} -f ./Dockerfile .  --platform ${PLATFORM} --build-arg BASE_IMAGE="${BASE_IMAGE}"
docker images
docker save -o "swoole-cli-builder-image.tar" ${IMAGE}



# debian 可设置的架构选项
# https://hub.docker.com/_/debian/tags

:<<'EOF'
linux/386
linux/amd64
linux/arm/v5
linux/arm/v7
linux/arm64/v8
linux/arm64
linux/mips64le
linux/ppc64le
linux/riscv64
linux/s390x
EOF

# Debian 全球镜像站
# https://www.debian.org/mirror/list
