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

cd ${__PROJECT__}

cd ${__PROJECT__}/

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export http_proxy="$2"
    export http_proxy="$2"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

cd ${__PROJECT__}

test -d ${__PROJECT__}/var || mkdir -p ${__PROJECT__}/var

composer update --no-dev

export SWOOLE_CLI_SKIP_DOWNLOAD=1
export SWOOLE_CLI_WITHOUT_DOCKER=1

php prepare.php --with-build-type=release --skip-download=1 +ds +inotify +apcu +protobuf +protobuf --without-docker=1
sh sapi/scripts/download-dependencies-use-aria2.sh
sh sapi/scripts/download-dependencies-use-git.sh

# for macos
php prepare.php --with-build-type=release --skip-download=1 +ds +apcu +protobuf +protobuf --without-docker=1 @macos --with-dependency-graph=1
sh sapi/scripts/download-dependencies-use-aria2.sh
sh sapi/scripts/download-dependencies-use-git.sh

# 系统环境需要提前安装好 graphviz
# 生成扩展依赖图

bash sapi/scripts/generate-dependency-graph.sh
