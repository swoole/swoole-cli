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

test -d ${__PROJECT__}/var/download-box/ || mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/
mkdir -p lib
mkdir -p ext


DOMAIN='https://swoole-cli.jingjingxyk.com/'
URL="${DOMAIN}/all-archive.zip"

test -f all-archive.zip || wget -O all-archive.zip ${URL}

# https://www.runoob.com/linux/linux-comm-unzip.html
# -o 不必先询问用户，unzip执行后覆盖原有文件。
# -n 解压缩时不要覆盖原有的文件。
unzip -o all-archive.zip
# unzip -n all-archive.zip


cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'


echo "download all-archive.zip ok ！"
