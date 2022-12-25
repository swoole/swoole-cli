#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

__ROOT__=$(
  cd ${__DIR__}/../
  pwd
)

{
  docker stop swoole-cli-build-dev-2
  docker rm swoole-cli-build-dev-2
} || {
  echo $?
}

image=phpswoole/swoole_cli_os:1.4

docker run --rm --name swoole-cli-build-dev-2 -d -v ${__PROJECT__}:/work -w /work $image tail -f /dev/null
