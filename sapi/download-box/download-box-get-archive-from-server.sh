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
  DOMAIN='https://github.com/swoole/swoole-cli/releases/download/v6.0.0.0/'
  ALL_DEPS_HASH="a55699ecee994032f33266dfa37eabb49f1f6d6b6b65cdcf7b881cac09c63bea"
else
  DOMAIN='https://github.com/swoole/build-static-php/releases/download/v1.6.0/'
  ALL_DEPS_HASH="771f8c695477be93da10847f3051fb054f0f829b242300e1ae2126b67f338664"
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

test -f all-deps.zip || curl -fSLo all-deps.zip ${URL}

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
