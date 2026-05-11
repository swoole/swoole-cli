#!/usr/bin/env bash

set -xu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../
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

APP_VERSION='v8.4.21'
APP_NAME='php-fpm'
VERSION='php-fpm-v2.4.0'
X_APP_VERSIONS=""
declare -A PHP_VESIONS
# PHP_VESIONS[0]="v8.1.34"
PHP_VESIONS[1]="v8.2.31"
PHP_VESIONS[2]="v8.3.31"
PHP_VESIONS[3]="v8.4.21"
PHP_VESIONS[4]="v8.5.6"

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
      X_VERSION=$(echo "$2" | grep -E '^v\d\.\d{1,2}\.\d{1,2}$')
    elif [ $OS = "linux" ]; then
      OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release | tr -d '\n' | tr -d '\"')
      if [ "$OS_RELEASE" = 'alpine' ]; then
        X_VERSION=$(echo "$2" | egrep -E '^v\d\.\d{1,2}\.\d{1,2}$')
      else
        X_VERSION=$(echo "$2" | grep -P '^v\d\.\d{1,2}\.\d{1,2}$')
      fi

    else
      X_VERSION=''
    fi

    if [[ -n $X_VERSION ]]; then
      {
        VERSION=$X_VERSION
      }
    else
      {
        echo '--version vx.x.x error !'
        exit 0
      }
    fi
    ;;
  --app-version)
    # 指定 PHP 版本
    if [ $OS = "macos" ]; then
      X_APP_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
    elif [ $OS = "linux" ]; then
      OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release | tr -d '\n' | tr -d '\"')
      if [ "$OS_RELEASE" = 'alpine' ]; then
        X_APP_VERSION=$(echo "$2" | egrep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
      else
        X_APP_VERSION=$(echo "$2" | grep -Po '^v\d\.\d{1,2}\.\d{1,2}')
      fi
    else
      X_APP_VERSION=''
    fi

    if [[ -n $X_APP_VERSION ]]; then
      {
        APP_VERSION=$X_APP_VERSION
      }
    else
      {
        echo '--php-version vx.x.x error !'
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

mkdir -p ${__PROJECT__}/var/artifacts/${APP_NAME}/${VERSION}
# shellcheck disable=SC2164
cd ${__PROJECT__}/var/artifacts/${APP_NAME}/${VERSION}

UNIX_DOWNLOAD_APP_RUNTIME() {
  local OS="$1"
  local ARCH="$2"
  local APP_VERSION="$3"
  local APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  local APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"
  test -f ${APP_RUNTIME}.tar.xz || curl -fSLo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}
}

UNIX_DOWNLOAD() {
  local APP_VERSION="$1"
  UNIX_DOWNLOAD_APP_RUNTIME "linux" "x64" "${APP_VERSION}"
  UNIX_DOWNLOAD_APP_RUNTIME "linux" "arm64" "${APP_VERSION}"
  UNIX_DOWNLOAD_APP_RUNTIME "macos" "x64" "${APP_VERSION}"
  UNIX_DOWNLOAD_APP_RUNTIME "macos" "arm64" "${APP_VERSION}"
}

DOWNLOAD() {
  CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"
  test -f cacert.pem || curl -LSo cacert.pem ${CACERT_DOWNLOAD_URL}
  # shellcheck disable=SC2068
  for i in ${!PHP_VESIONS[@]}; do
    echo -e "${PHP_VESIONS[$i]}"
    UNIX_DOWNLOAD "${PHP_VESIONS[$i]}"
  done

}

DOWNLOAD

mkdir -p ${__PROJECT__}/pool/${APP_NAME}
cp -rf ${__PROJECT__}/var/artifacts/${APP_NAME}/${VERSION}/* ${__PROJECT__}/pool/${APP_NAME}
