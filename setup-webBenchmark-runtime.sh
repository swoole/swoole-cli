#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}

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
'aarch64' | 'arm64' )
  ARCH="arm64"
  ARCH="arm"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

APP_VERSION='0.9'
APP_NAME='webBenchmark'
VERSION='0.9'

mkdir -p bin/runtime
mkdir -p var/runtime

cd ${__PROJECT__}/var/runtime

APP_DOWNLOAD_URL="https://github.com/maintell/webBenchmark/releases/download/${VERSION}/${APP_NAME}_${OS}_${ARCH}"
CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://github.com/maintell/webBenchmark/releases/download/${VERSION}/${APP_NAME}_${OS}_${ARCH}.exe"
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




APP_RUNTIME="${APP_NAME}_${OS}_${ARCH}"

if [ $OS = 'windows' ]; then
  {
    APP_RUNTIME="${APP_NAME}_${OS}_${ARCH}.exe"
    exit 0
  }
else
  test -f ${APP_RUNTIME} || curl -LSo ${APP_RUNTIME} ${APP_DOWNLOAD_URL}

  chmod a+x ${APP_RUNTIME}
  cp -rf ${__PROJECT__}/var/runtime/${APP_RUNTIME} ${__PROJECT__}/bin/runtime/${APP_RUNTIME}
fi

cd ${__PROJECT__}/var/runtime

cd ${__PROJECT__}/

set +x

echo " 测网速工具 "
echo " USE webBenchmark RUNTIME :"
echo " "
echo " export PATH=\"${__PROJECT__}/bin/runtime:\$PATH\" "
echo " "
echo " docs :  https://github.com/maintell/webBenchmark"
echo " "
echo " example  :  webBenchmark_linux_x64 -c 32 -s https://target.url"
echo " "
export PATH="${__PROJECT__}/bin/runtime:$PATH"
