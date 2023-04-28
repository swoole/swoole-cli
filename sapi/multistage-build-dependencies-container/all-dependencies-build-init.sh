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

OS=$(uname -s)
ARCH=$(uname -m)

case $OS in
'Linux')
  OS="linux"
  ;;
'Darwin')
  OS="macos"
  ;;
*)
  echo '暂未配置的 OS '
  exit 0
  ;;

esac

case $ARCH in
'x86_64')
  ARCH="x64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

VERSION='v5.0.2'
SWOOLE_CLI_RUNTIME="swoole-cli-${VERSION}-${OS}-${ARCH}"

mkdir -p ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var


test -d swoole-cli || git clone -b build_native_php  --depth=1 --single-branch  https://github.com/jingjingxyk/swoole-cli.git
test -d swoole-cli &&  git -C swoole-cli  pull --depth=1 --rebase=true

mkdir -p  ${__PROJECT__}/var/runtime
cd ${__PROJECT__}/var/runtime

SWOOLE_CLI_DOWNLOAD_URL="https://github.com/swoole/swoole-src/releases/download/${VERSION}/swoole-cli-${VERSION}-${OS}-${ARCH}.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"

mirror=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    mirror="$2"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$mirror" in
china)
  SWOOLE_CLI_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/swoole-cli-${VERSION}-${OS}-${ARCH}.tar.xz"
  COMPOSER_DOWNLOAD_URL="https://mirrors.aliyun.com/composer/composer.phar"
  ;;

esac

test -f ${SWOOLE_CLI_RUNTIME}.tar.xz || wget -O ${SWOOLE_CLI_RUNTIME}.tar.xz ${SWOOLE_CLI_DOWNLOAD_URL}
test -f ${SWOOLE_CLI_RUNTIME}.tar || xz -d -k ${SWOOLE_CLI_RUNTIME}.tar.xz
test -f swoole-cli || tar -xvf ${SWOOLE_CLI_RUNTIME}.tar
chmod a+x swoole-cli

test -f composer.phar || wget -O composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar



cd ${__PROJECT__}/var

cd swoole-cli
mkdir -p pool/lib
mkdir -p pool/ext
sh sapi/download-box/download-box-get-archive-from-container.sh

cd ${__PROJECT__}/var

awk 'BEGIN { cmd="cp -ri libraries/* swoole-cli/pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri extensions/* swoole-cli/pool/ext"; print "n" |cmd; }'

