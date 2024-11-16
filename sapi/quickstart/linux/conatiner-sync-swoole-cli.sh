#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

# 容器之间同步 /usr/local/swoole-cli 目录

test -d /tmp/swoole-cli-builder && rm -rf /tmp/swoole-cli-builder

docker cp swoole-cli-alpine-dev:/usr/local/swoole-cli/ /tmp/swoole-cli-builder

docker cp /tmp/swoole-cli-builder swoole-cli-builder:/usr/local/swoole-cli


# docker cp /tmp/swoole-cli-builder swoole-cli-alpine-dev:/usr/local/swoole-cli

# 运行的容器挂载目录
# docker exec -it swoole-cli-builder mount --bind /tmp/swoole-cli-builder /usr/local/swoole-cli/
