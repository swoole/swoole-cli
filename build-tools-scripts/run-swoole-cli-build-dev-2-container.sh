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

{
  docker stop swoole-cli-build-dev-2
  docker rm swoole-cli-build-dev-2
} || {
  echo $?
}

cd ${__DIR__}
test -f swoole-cli-build-dev-2-container.txt && image=$(cat swoole-cli-build-dev-1-container.txt)
test -f swoole-cli-build-dev-2-container.txt || image=phpswoole/swoole_cli_os:1.4

# image=phpswoole/swoole_cli_os:1.4

docker run --rm --name swoole-cli-build-dev-2 -d -v ${__PROJECT__}:/work -w /work $image tail -f /dev/null
