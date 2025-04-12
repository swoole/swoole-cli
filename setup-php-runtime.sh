#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}
shopt -s expand_aliases
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
  case $OS in
  'MSYS_NT'*)
    OS="windows"
    ;;
  'MINGW64_NT'*)
    OS="windows"
    ;;
  *)
    echo '暂未配置的 OS '
    exit 0
    ;;
  esac
  ;;
esac

case $ARCH in
'x86_64')
  ARCH="x64"
  ;;
'aarch64' | 'arm64')
  ARCH="arm64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

APP_VERSION='v5.1.3'
APP_NAME='swoole-cli'
VERSION='v5.1.3.0'

cd ${__PROJECT__}
mkdir -p bin/
mkdir -p runtime/
mkdir -p var/runtime
APP_RUNTIME_DIR=${__PROJECT__}/runtime
mkdir -p ${APP_RUNTIME_DIR}

cd ${__PROJECT__}/var/runtime

APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"
CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"
fi

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
    NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china)
  APP_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  COMPOSER_DOWNLOAD_URL="https://mirrors.tencent.com/composer/composer.phar"
  if [ $OS = 'windows' ]; then
    APP_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"
  fi
  ;;

esac

test -f composer.phar || curl -fSLo composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar

test -f cacert.pem || curl -fSLo cacert.pem ${CACERT_DOWNLOAD_URL}

APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"

if [ $OS = 'windows' ]; then
  {
    APP_RUNTIME="${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}"
    test -f ${APP_RUNTIME}.zip || curl -fSLo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
    test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
    unzip "${APP_RUNTIME}.zip"
    exit 0
  }
else
  test -f ${APP_RUNTIME}.tar.xz || curl -fSLo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}
  test -f ${APP_RUNTIME}.tar || xz -d -k ${APP_RUNTIME}.tar.xz
  test -f swoole-cli && rm -f swoole-cli
  tar -xvf ${APP_RUNTIME}.tar
  chmod a+x swoole-cli
  cp -f ${__PROJECT__}/var/runtime/swoole-cli ${APP_RUNTIME_DIR}/php
fi

cd ${__PROJECT__}/var/runtime

cp -f ${__PROJECT__}/var/runtime/composer.phar ${APP_RUNTIME_DIR}/composer
cp -f ${__PROJECT__}/var/runtime/cacert.pem ${APP_RUNTIME_DIR}/cacert.pem

cat >${APP_RUNTIME_DIR}/php.ini <<EOF
curl.cainfo="${APP_RUNTIME_DIR}/cacert.pem"
openssl.cafile="${APP_RUNTIME_DIR}/cacert.pem"
swoole.use_shortname=off
display_errors = On
error_reporting = E_ALL

upload_max_filesize="128M"
post_max_size="128M"
memory_limit="1G"
date.timezone="UTC"

opcache.enable=On
opcache.enable_cli=On
opcache.jit=1225
opcache.jit_buffer_size=128M

expose_php=Off
phar.readonly=0


EOF

cd ${__PROJECT__}/

export PATH="${APP_RUNTIME_DIR}:$PATH"
alias php="php -c ${APP_RUNTIME_DIR}/php.ini"
php -v
php --ri curl
php --ri openssl
php --ri swoole

set +x

echo " "
echo " USE PHP RUNTIME : "
echo " "
echo " export PATH=\"${APP_RUNTIME_DIR}:\$PATH\" "
echo " "
echo " shopt -s expand_aliases "
echo " "
echo " alias php='php -d curl.cainfo=${APP_RUNTIME_DIR}/cacert.pem -d openssl.cafile=${APP_RUNTIME_DIR}/cacert.pem' "
echo " OR "
echo " alias php='php -c${APP_RUNTIME_DIR}/php.ini' "
echo " "
test $OS="macos" && echo "sudo xattr -d com.apple.quarantine ${APP_RUNTIME_DIR}/php"
echo " "
