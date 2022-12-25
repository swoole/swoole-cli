#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}

{
  docker stop swoole-cli-build-dev-1
  docker rm swoole-cli-build-dev-1
} || {
  echo $?
}


image=docker.io/phpswoole/swoole_cli_os:build-dev-1-alpine-edge

docker run --rm --name swoole-cli-build-dev-1 -d -v ${__PROJECT__}:/work -w /work $image tail -f /dev/null

