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
  docker stop swoole-cli-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}

IMAGE=alpine:3.16

:<<'EOF'
   启动此容器

   已经内置了 php 、composer 、 编译好的依赖库


EOF

OS=$(uname -s)
ARCH=$(uname -m)

case $ARCH in
'x86_64')
  IMAGE=docker.io/phpswoole/swoole-cli-builder:all-dependencies-alpine-swoole-cli-x86_64-20230504T133110Z
  IMAGE=docker.io/jingjingxyk/build-swoole-cli:all-dependencies-alpine-swoole-cli-x86_64-20230505T120137Z
  ;;
'aarch64')
  IMAGE=docker.io/phpswoole/swoole-cli-builder:1.7-arm64
  IMAGE=docker.io/jingjingxyk/build-swoole-cli:all-dependencies-alpine-swoole-cli-aarch64-20230505T153618Z
  ;;
*)
  echo "此 ${ARCH} 架构的容器 容器未配置"
  exit 0
  ;;
esac



cd ${__DIR__}
docker run --rm --name swoole-cli-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
