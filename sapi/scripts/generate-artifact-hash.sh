#!/usr/bin/env bash

set -xu
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

APP_VERSION='v5.1.6'
APP_NAME='swoole-cli'
VERSION='v5.1.6.0'

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    export NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    ;;
  --version)
    # 指定发布 TAG
    if [ $OS = "macos" ]; then
      X_VERSION=$(echo "$2" | grep -E '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
      X_APP_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
    elif [ $OS = "linux" ]; then
      OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release | tr -d '\n' | tr -d '\"')
      if [ "$OS_RELEASE" = 'alpine' ]; then
        X_VERSION=$(echo "$2" | egrep -E '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
        X_APP_VERSION=$(echo "$2" | egrep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
      else
        X_VERSION=$(echo "$2" | grep -P '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
        X_APP_VERSION=$(echo "$2" | grep -Po '^v\d\.\d{1,2}\.\d{1,2}')
      fi

    else
      X_VERSION=''
      X_APP_VERSION=''
    fi

    if [[ -n $X_VERSION ]] && [[ -n $X_APP_VERSION ]]; then
      {
        VERSION=$X_VERSION
        APP_VERSION=$X_APP_VERSION
      }
    else
      {
        echo '--version vx.x.x error !'
        exit 0
      }
    fi
    ;;
  --*)
    echo "Illegal option $1"
    exit 0
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

mkdir -p ${__PROJECT__}/var/artifact-hash/${VERSION}
cd ${__PROJECT__}/var/artifact-hash/${VERSION}

UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME() {
  OS="$1"
  ARCH="$2"

  APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"
  test -f ${APP_RUNTIME}.tar.xz || curl -fSLo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}

}

UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "linux" "x64"
UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "linux" "arm64"
UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "macos" "x64"
UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "macos" "arm64"

ARCH="x64"
APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"

APP_RUNTIME="${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}"
test -f ${APP_RUNTIME}.zip || curl -fSLo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
test -f all-deps.zip || curl -fSLo all-deps.zip https://github.com/swoole/swoole-cli/releases/download/${VERSION}/all-deps.zip

ls -p | grep -v '/$' | xargs sha256sum

ls -p | grep -v '/$' | xargs sha256sum >${__PROJECT__}/${VERSION}-sha256sum
