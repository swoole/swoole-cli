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

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

DOMAIN='https://github.com/jingjingxyk/swoole-cli/releases/download/all-archive-2024-01-09/'
case "$MIRROR" in
china)
  DOMAIN='https://swoole-cli.jingjingxyk.com/'
  ;;
esac


mkdir -p  pool/lib
mkdir -p  pool/ext

mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/


URL="${DOMAIN}/all-archive.zip"

test -f  all-archive.zip || curl -LSo all-archive.zip ${URL}

unzip -n all-archive.zip

cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'

echo "download all-archive.zip ok ！"
