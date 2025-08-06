#!/usr/bin/env bash

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

cd ${__DIR__}

mkdir -p /dev/shm/swoole-cli/thirdparty/
mkdir -p /dev/shm/swoole-cli/ext/
mkdir -p /dev/shm/swoole-cli/var/

docker run --rm --name swoole-cli-alpine-dev -d \
  -v ${__PROJECT__}:/work \
  -v /dev/shm/swoole-cli/thirdparty/:/work/thirdparty/ \
  -v /dev/shm/swoole-cli/ext/:/work/ext/ \
  -v /dev/shm/swoole-cli/var/:/work/var/ \
  -w /work --init $IMAGE tail -f /dev/null
