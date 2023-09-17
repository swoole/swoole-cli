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

mkdir -p ${__PROJECT__}/bin/runtime
cd ${__PROJECT__}/bin/runtime

set +x
if [[ ! -f php ]] || [[ ! -f composer ]]; then
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
chmod a+x php
chmod a+x composer

export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"


cd ${__PROJECT__}

mkdir -p pool/lib
mkdir -p pool/ext

cd ${__PROJECT__}

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export http_proxy="$2"
    export https_proxy="$2"
    export no_proxy="0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,127.0.0.0/8,172.16.0.0/12,192.168.0.0/16"
    shift
    ;;
    --mirror)
      ## 借助 download-box 获得已经准备好的 依赖库源码 ，缩减下载时间  存放于 var目录
      bash sapi/download-box/download-box-get-archive-from-server.sh
      MIRROR="$2"
      shift
      ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


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
--without-docker=1 \
--with-build-type=release

cd ${__PROJECT__}/
