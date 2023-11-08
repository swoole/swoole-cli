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

mkdir -p  pool/lib
mkdir -p  pool/ext

test -d ${__PROJECT__}/var || mkdir -p ${__PROJECT__}/var

cd ${__PROJECT__}/var
mkdir -p lib
mkdir -p ext

DOMAIN='https://swoole-cli.jingjingxyk.com/'
URL="${DOMAIN}/all-archive.zip"

test -f  all-archive.zip || wget -O all-archive.zip ${URL}

unzip -n all-archive.zip

cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/ext/* pool/ext"; print "n" |cmd; }'

echo "download all-archive.zip ok ！"
