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

mkdir -p ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var/runtime

set +x
if [[ ! -f swoole-cli ]] || [[ ! -f composer.phar ]]; then
  echo ""
  echo ""
  echo "please run： bash sapi/quickstart/setup-php-runtime.sh "
  echo ""
  echo ""
  echo ""
  echo ""
  exit 0
fi

chmod a+x swoole-cli
chmod a+x composer.phar

set -x

cd ${__PROJECT__}

## 借助 download-box 获得已经准备好的 依赖库源码 ，缩减下载时间  存放于 var目录
sh sapi/download-box/download-box-get-archive-from-server.sh

cd ${__PROJECT__}

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

cd ${__PROJECT__}/var

test -d swoole-cli || git clone -b build_native_php --depth=1 --single-branch https://github.com/jingjingxyk/swoole-cli.git
test -d swoole-cli && git -C swoole-cli pull --depth=1

cd ${__PROJECT__}/var/swoole-cli

mkdir -p pool/lib
mkdir -p pool/ext

cd ${__PROJECT__}/var

awk 'BEGIN { cmd="cp -ri libraries/* swoole-cli/pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri extensions/* swoole-cli/pool/ext"; print "n" |cmd; }'

cd ${__PROJECT__}/var/swoole-cli
composer update --no-dev
php prepare.php --with-build-type=dev --with-dependency-graph=1 +apcu +ds +inotify --without-docker
php prepare.php --with-build-type=dev --with-dependency-graph=1 +apcu +ds @macos --without-docker

cd ${__PROJECT__}/
