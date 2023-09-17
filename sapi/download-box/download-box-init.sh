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

cd ${__PROJECT__}/

MIRROR=""
while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export http_proxy="$2"
    export https_proxy="$2"
    export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
    export NO_PROXY="\${NO_PROXY},127.0.0.1,localhost"
    shift
    ;;
  --mirror)
    MIRROR="$2"
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
export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"
case "$MIRROR" in
  aliyun)
  # shellcheck disable=SC2034
  MIRROR_SITE='aliyun'
  composer config  repo.packagist composer https://mirrors.aliyun.com/composer/
  ;;
  tencent)
  # shellcheck disable=SC2034
  MIRROR_SITE='tencent'
  composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
  ;;
  *)
    echo 'no found mirror site'
    ;;
esac


composer update  --optimize-autoloader
composer config -g --unset repos.packagist


php prepare.php  +ds +inotify +apcu  +pgsql +pdo_pgsql \
--with-swoole-pgsql=1 \
--with-libavif=1 \
--without-docker=1 --with-skip-download=1 \
--with-dependency-graph=1 \
--with-build-type=release


cd ${__PROJECT__}
# 生成扩展依赖图
bash sapi/extension-dependency-graph/generate-dependency-graph.sh

cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-aria2.sh
cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-git.sh
cd ${__PROJECT__}


