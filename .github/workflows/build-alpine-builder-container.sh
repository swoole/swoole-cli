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
cd ${__DIR__}
cd ${__PROJECT__}
mkdir -p var/build-github-action-container/
cd ${__PROJECT__}/var/build-github-action-container/

cp -f ${__PROJECT__}/sapi/quickstart/linux/alpine-init.sh .

cat > Dockerfile <<'EOF'
FROM alpine:3.18

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
ENV TZ=Etc/UTC

ADD ./alpine-init.sh /alpine-init.sh

RUN sh /alpine-init.sh
# RUN sh /alpine-init.sh --mirror china

RUN uname -m
RUN mkdir /work

WORKDIR /work
ENTRYPOINT ["tini", "--"]

EOF



PLATFORM='linux/amd64'

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
docker buildx build -t ${IMAGE} -f ./Dockerfile .  --platform ${PLATFORM}

docker save -o "swoole-cli-builder-image.tar" ${IMAGE}


# 可设置的架构选项
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
