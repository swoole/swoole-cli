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

## tencentyun upload oss
## 下载与安装配置
## https://cloud.tencent.com/document/product/436/63144

APP_VERSION="v1.0.3"
APP_NAME="coscli"
APP_RUNTIME="${APP_NAME}-${APP_VERSION}"

OS=$(uname -s)
ARCH=$(uname -m)

case $OS in
'Linux')
  APP_RUNTIME+='-linux'
  ;;
'Darwin')
  APP_RUNTIME+='-darwin'
  ;;
*)
  echo 'NO SUPPORT OS'
  exit 0
  ;;
esac

case $ARCH in
'x86_64')
  APP_RUNTIME+='-amd64'
  ;;
'aarch64' | 'arm64')
  APP_RUNTIME+='-arm64'
  ;;
*)
  echo 'NO SUPPORT ARCH '
  exit 0
  ;;
esac

mkdir -p ${__PROJECT__}/var/upload-release-oss/
cd ${__PROJECT__}/var/upload-release-oss/

test -f ${APP_RUNTIME} || curl -fSLo ${APP_RUNTIME} https://github.com/tencentyun/coscli/releases/download/${APP_VERSION}/${APP_RUNTIME}

chmod a+x ${APP_RUNTIME}
cp -f ${APP_RUNTIME} coscli
APP="${__PROJECT__}/var/upload-release-oss/coscli --endpoint cos.ap-shanghai.myqcloud.com "

${APP} --help

SWOOLE_CLI_VERSION='v5.1.6.0'
SWOOLE_VERSION='v5.1.6'
while [ $# -gt 0 ]; do
  case "$1" in
  --swoole-cli-version)
    SWOOLE_CLI_VERSION="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

if [ -d ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION} ]; then
  SWOOLE_VERSION=$(echo ${SWOOLE_CLI_VERSION} | awk -F '.' '{ printf "%s.%s.%s" ,$1,$2,$3 }')
else
  echo "please download release artifact !"
  echo "bash ${__PROJECT__}/sapi/scripts/generate-artifact-hash.sh --version ${SWOOLE_CLI_VERSION}"
  exit 0
fi

cd ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION}

${APP} cp swoole-cli-${SWOOLE_VERSION}-cygwin-x64.zip cos://examplebucket-1250000000/
${APP} cp swoole-cli-${SWOOLE_VERSION}-linux-arm64.tar.xz cos://examplebucket-1250000000/
${APP} cp swoole-cli-${SWOOLE_VERSION}-linux-x64.tar.xz cos://examplebucket-1250000000/
${APP} cp swoole-cli-${SWOOLE_VERSION}-macos-arm64.tar.xz cos://examplebucket-1250000000/
${APP} cp swoole-cli-${SWOOLE_VERSION}-macos-x64.tar.xz cos://examplebucket-1250000000/

cd ${__PROJECT__}
