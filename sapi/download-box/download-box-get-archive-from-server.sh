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

mkdir -p pool/lib
mkdir -p pool/ext

test -d ${__PROJECT__}/var || mkdir -p ${__PROJECT__}/var

cd ${__PROJECT__}/var

DOMAIN='http://127.0.0.1:8000'
DOMAIN='https://swoole-cli.jingjingxyk.com/'
URL="${DOMAIN}/all-archive.zip"

test -f all-archive.zip || wget -O all-archive.zip ${URL}

unzip -n all-archive.zip

cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/libraries/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/extensions/* pool/ext"; print "n" |cmd; }'

echo "download all-archive.zip ok ！"
