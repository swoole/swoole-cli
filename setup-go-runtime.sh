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
  OS="darwin"
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
  ARCH="amd64"
  ;;
'aarch64' | 'arm64')
  ARCH="arm64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

APP_VERSION='1.22.5'
APP_NAME='go'
VERSION='1.22.5'

mkdir -p bin/runtime
mkdir -p var/runtime

cd ${__PROJECT__}/var/runtime

: <<'EOF'

https://go.dev/dl/go1.22.5.windows-amd64.msi

https://go.dev/dl/go1.22.5.darwin-arm64.pkg

https://go.dev/dl/go1.22.5.darwin-amd64.pkg
https://go.dev/dl/go1.22.5.linux-amd64.tar.gz
https://go.dev/dl/go1.21.12.linux-arm64.tar.gz
https://go.dev/dl/go1.21.12.windows-amd64.zip
https://go.dev/dl/go1.21.12.darwin-arm64.tar.gz
https://go.dev/dl/go1.21.12.darwin-amd64.tar.gz

EOF

APP_DOWNLOAD_URL="https://go.dev/dl/${APP_NAME}${APP_VERSION}.${OS}-${ARCH}.tar.gz"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://go.dev/dl/${APP_NAME}${APP_VERSION}.${OS}-${ARCH}.zip"
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
  APP_DOWNLOAD_URL="https://golang.google.cn/dl/${APP_NAME}${APP_VERSION}.${OS}-${ARCH}.tar.gz"
  if [ $OS = 'windows' ]; then
    APP_DOWNLOAD_URL="https://golang.google.cn/dl/${APP_NAME}${APP_VERSION}.${OS}-${ARCH}.zip"
  fi
  ;;
esac

APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"
if [ $OS = 'windows' ]; then
  test -f ${APP_RUNTIME}.zip || curl -LSo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
  test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
  unzip "${APP_RUNTIME}.zip"
  exit 0
else
  test -f ${APP_RUNTIME}.tar.gz || curl -LSo ${APP_RUNTIME}.tar.gz ${APP_DOWNLOAD_URL}
  test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
  tar -xvf ${APP_RUNTIME}.tar.gz
  test -d ${__PROJECT__}/bin/runtime/go && rm -rf ${__PROJECT__}/bin/runtime/go
  mv go ${__PROJECT__}/bin/runtime/go
fi

cd ${__PROJECT__}/

set +x

echo " "
echo " USE PHP RUNTIME :"
echo " "
echo " export PATH=\"${__PROJECT__}/bin/runtime/go/bin/:\$PATH\" "
echo " "
export PATH="${__PROJECT__}/bin/runtime/go/bin/:$PATH"
go version
