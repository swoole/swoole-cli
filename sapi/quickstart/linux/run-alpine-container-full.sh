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

{
  docker stop swoole-cli-alpine-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}

IMAGE=alpine:3.18

:<<'EOF'
   启动此容器

   已经内置了 php 、composer 、 编译好的依赖库


EOF

OS=$(uname -s)
ARCH=$(uname -m)

MIRROR=""
DEV_SHM=0

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --dev-shm) #使用 /dev/shm 目录加快构建速度
    DEV_SHM=1
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


case $ARCH in
'x86_64')
  TAG=all-dependencies-alpine-3.17-php8-v1.0.0-x86_64-20231113T125520Z
  IMAGE=docker.io/jingjingxyk/build-swoole-cli:${TAG}
  if [ "$MIRROR" = 'china' ] ; then
    IMAGE=registry.cn-beijing.aliyuncs.com/jingjingxyk-public/app:${TAG}
  fi
  ;;
'aarch64')
  TAG=all-dependencies-alpine-3.18-php8-v1.0.0-aarch64-20240618T091126Z
  IMAGE=docker.io/jingjingxyk/build-swoole-cli:${TAG}
    if [ "$MIRROR" = 'china' ] ; then
      IMAGE=registry.cn-hangzhou.aliyuncs.com/jingjingxyk-public/app:${TAG}
    fi
  ;;
*)
  echo "此 ${ARCH} 架构的容器 容器未配置"
  exit 0
  ;;
esac


cd ${__DIR__}

if [ $DEV_SHM -eq 1 ] ; then
  mkdir -p /dev/shm/swoole-cli/thirdparty/
  mkdir -p /dev/shm/swoole-cli/ext/
  docker run --rm --name swoole-cli-alpine-dev -d -v ${__PROJECT__}:/work -v /dev/shm/swoole-cli/thirdparty/:/work/thirdparty/ -v /dev/shm/swoole-cli/ext/:/work/ext/ -w /work $IMAGE tail -f /dev/null
else
  docker run --rm --name swoole-cli-alpine-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
fi

