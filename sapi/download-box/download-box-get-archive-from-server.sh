#!/usr/bin/env bash

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

mkdir -p pool/lib
mkdir -p pool/ext

mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/

if [ -f "${__PROJECT__}/sapi/PHP-VERSION.conf" ]; then
  DOMAIN='https://github.com/swoole/swoole-cli/releases/download/v5.1.5.1/'
  ALL_DEPS_HASH="bdd159b93fd8217e89d206aeb22bf7a8295553db0aff332f049b9025feb31766"
else
  DOMAIN='https://github.com/swoole/build-static-php/releases/download/v1.5.2/'
  ALL_DEPS_HASH="9408a86d9e50a07548eb11406f2448599e792cac3c999dcb35d308790c18e3ba"
fi

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    if [ "$2" = 'china' ]; then
      DOMAIN='https://swoole-cli.jingjingxyk.com/'
      if [ ! -f "${__PROJECT__}/sapi/PHP-VERSION.conf" ]; then
        DOMAIN='https://php-cli.jingjingxyk.com/'
      fi
    fi
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

URL="${DOMAIN}/all-deps.zip"

test -f all-deps.zip || curl -Lo all-deps.zip ${URL}

# hash 签名
HASH=$(sha256sum all-deps.zip | awk '{print $1}')

# 签名验证失败，删除下载文件
if [ ${HASH} != ${ALL_DEPS_HASH} ]; then
  echo 'hash signature is invalid ！'
  rm -f all-deps.zip
  echo '                       '
  echo ' Please Download Again '
  echo '                       '
  exit 0
fi

unzip -n all-deps.zip

cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'

echo "download all-archive.zip ok ！"
