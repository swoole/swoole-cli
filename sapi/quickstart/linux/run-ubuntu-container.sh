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
  docker stop swoole-cli-ubuntu-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}
IMAGE=ubuntu:22.04

cd ${__DIR__}
docker run --rm --name swoole-cli-ubuntu-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
