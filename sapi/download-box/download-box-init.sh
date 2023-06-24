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

export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-dev --optimize-autoloader

php prepare.php --with-build-type=release +ds +inotify +apcu +protobuf +pgsql +pdo_pgsql --with-swoole-pgsql=1 --without-docker=1 --with-skip-download=1
sh sapi/scripts/download-dependencies-use-aria2.sh
sh sapi/scripts/download-dependencies-use-git.sh

# 生成扩展依赖图

bash sapi/scripts/generate-dependency-graph.sh
