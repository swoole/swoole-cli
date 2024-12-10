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

APP_VERSION='v8.3.13'
APP_NAME='php-cli'
VERSION='v1.6.0'

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
  --php-version)
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

mkdir -p ${__PROJECT__}/var/artifact-hash/${VERSION}
cd ${__PROJECT__}/var/artifact-hash/${VERSION}

UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME() {
  OS="$1"
  ARCH="$2"
  APP_VERSION="$3"
  APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"
  test -f ${APP_RUNTIME}.tar.xz || curl -LSo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}

  APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}-debug.tar.xz"
  APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}-debug"
  test -f ${APP_RUNTIME}.tar.xz || curl -LSo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}
}

UNIX_DOWNLOAD() {
  APP_VERSION="$1"
  UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "linux" "x64" "${APP_VERSION}"
  UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "linux" "arm64" "${APP_VERSION}"
  UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "macos" "x64" "${APP_VERSION}"
  UNIX_DOWNLOAD_SWOOLE_CLIE_RUNTIME "macos" "arm64" "${APP_VERSION}"
}

WINDOWS_DOWNLOAD_SWOOLE_CLIE_RUNTIME() {
  APP_VERSION="$1"
  ARCH="x64"
  APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"

  APP_RUNTIME="${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}"
  test -f ${APP_RUNTIME}.zip || curl -LSo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
  test -f all-deps.zip || curl -LSo all-deps.zip https://github.com/swoole/swoole-cli/releases/download/v5.1.5.1/all-deps.zip

}
WINDOWS_DOWNLOAD() {
  WINDOWS_DOWNLOAD_SWOOLE_CLIE_RUNTIME "$1"
}

RUN_DOWNLOAD() {
  UNIX_DOWNLOAD "$1"
  WINDOWS_DOWNLOAD "$1"
}

DOWNLOAD() {
  declare -A PHP_VERSIONS
  PHP_VERSIONS[0]="v8.2.25"
  PHP_VERSIONS[1]="v8.1.30"
  PHP_VERSIONS[2]="v8.3.13"
  PHP_VERSIONS[3]="v8.4.1"
  for i in "${!PHP_VERSIONS[@]}"; do
    # echo ${PHP_VERSIONS[$i]}
    RUN_DOWNLOAD "${PHP_VERSIONS[$i]}"
  done

}

DOWNLOAD

ls -p | grep -v '/$' | xargs sha256sum

ls -p | grep -v '/$' | xargs sha256sum >${__PROJECT__}/${VERSION}-sha256sum
