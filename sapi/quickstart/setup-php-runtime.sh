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
'aarch64')
  ARCH="arm64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

VERSION='v5.0.3'
if [[ $OS = 'macos' ]]; then
  VERSION='v5.0.1'
fi
mkdir -p bin/runtime
mkdir -p var/runtime

cd ${__PROJECT__}/var/runtime

SWOOLE_CLI_DOWNLOAD_URL="https://github.com/swoole/swoole-src/releases/download/${VERSION}/swoole-cli-${VERSION}-${OS}-${ARCH}.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"

if [[ $ARCH = 'arm64'  ]] && [[ $OS = 'linux' ]]; then
  SWOOLE_CLI_DOWNLOAD_URL='https://github.com/jingjingxyk/swoole-cli/releases/download/build-native-php-v0.1.1/swoole-cli-v5.0.3-linux-arm64.tar.xz'
fi

mirror=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    mirror="$2"
    shift
    ;;
  --proxy)
    export http_proxy="$2"
    export https_proxy="$2"
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

SWOOLE_CLI_RUNTIME="swoole-cli-${VERSION}-${OS}-${ARCH}"

test -f ${SWOOLE_CLI_RUNTIME}.tar.xz || wget -O ${SWOOLE_CLI_RUNTIME}.tar.xz ${SWOOLE_CLI_DOWNLOAD_URL}
test -f ${SWOOLE_CLI_RUNTIME}.tar || xz -d -k ${SWOOLE_CLI_RUNTIME}.tar.xz
test -f swoole-cli || tar -xvf ${SWOOLE_CLI_RUNTIME}.tar
chmod a+x swoole-cli

test -f composer.phar || wget -O composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar

cd ${__PROJECT__}/var/runtime

cp -f ${__PROJECT__}/var/runtime/swoole-cli ${__PROJECT__}/bin/runtime/php
cp -f ${__PROJECT__}/var/runtime/composer.phar ${__PROJECT__}/bin/runtime/composer

cd ${__PROJECT__}/

set +x

echo " "
echo " "
echo " USE  PHP  rumtime :"
echo " "
echo " export PATH=\"${__PROJECT__}/bin/runtime:\$PATH\" "
echo " "
echo " "

export PATH="${__PROJECT__}/bin/runtime:$PATH"
