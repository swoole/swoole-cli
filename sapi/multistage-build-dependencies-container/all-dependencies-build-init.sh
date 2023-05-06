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

mkdir -p ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var/runtime

set +x
if [[ ! -f swoole-cli ]] || [[ ! -f composer.phar ]]; then
  echo ""
  echo ""
  echo "please run： bash sapi/quickstart/setup-php-runtime.sh "
  echo "or use mirror "
  echo "please run： bash sapi/quickstart/setup-php-runtime.sh --mirror china "
  echo "or use proxy "
  echo "please run： bash sapi/quickstart/setup-php-runtime.sh --proxy http://127.0.0.1:1080 "
  echo ""
  exit 0
fi
set -x
chmod a+x swoole-cli
chmod a+x composer.phar

cd ${__PROJECT__}

## 借助 download-box 获得已经准备好的 依赖库源码 ，缩减下载时间  存放于 var目录
sh sapi/download-box/download-box-get-archive-from-server.sh

cd ${__PROJECT__}

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export http_proxy="$2"
    export https_proxy="$2"
    export no_proxy="0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,127.0.0.0/8,172.16.0.0/12,192.168.0.0/16"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

cd ${__PROJECT__}/var

GIT_BRANCH=main
test -d swoole-cli && git -C swoole-cli pull origin ${GIT_BRANCH} --depth=1 --progress --rebase=true --allow-unrelated-histories
test -d swoole-cli || git clone -b ${GIT_BRANCH} --depth=1 https://github.com/swoole/swoole-cli.git

cd ${__PROJECT__}/var/swoole-cli

mkdir -p pool/lib
mkdir -p pool/ext

cd ${__PROJECT__}/var

awk 'BEGIN { cmd="cp -ri libraries/* swoole-cli/pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri extensions/* swoole-cli/pool/ext"; print "n" |cmd; }'

cd ${__PROJECT__}/var/swoole-cli

export COMPOSER_ALLOW_SUPERUSER=1
composer update --no-dev --optimize-autoloader

php prepare.php --with-build-type=dev --with-dependency-graph=1 +apcu +ds +inotify --without-docker

cd ${__PROJECT__}/
