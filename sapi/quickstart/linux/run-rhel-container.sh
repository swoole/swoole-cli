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
  docker stop swoole-cli-rhel-dev
  sleep 5
} || {
  echo $?
}
cd ${__DIR__}

# IMAGE=oraclelinux:9

IMAGE=almalinux:9
IMAGE=rockylinux:9

cd ${__DIR__}
docker run --rm --name swoole-cli-rhel-dev -d -v ${__PROJECT__}:/work -w /work $IMAGE tail -f /dev/null
