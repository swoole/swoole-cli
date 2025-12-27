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

APP_VERSION="v1.0.7"
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
  case $OS in
  'MSYS_NT'* | 'MINGW64_NT'* | 'CYGWIN_NT'*)
    OS="windows"
    APP_RUNTIME+='-windows'
    echo ' 暂不支持 '
    exit 0
    ;;
  *)
    echo 'NO SUPPORT OS'
    exit 0
    ;;
  esac

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
  echo 'NO SUPPORT CPU ARCH '
  exit 0
  ;;
esac

SWOOLE_CLI_VERSION='v6.0.0.0'
SWOOLE_VERSION='v6.0.0'
UPLOAD_FILE=''
UPLOAD_TYPE=''
PROXY_OPTION=''
while [ $# -gt 0 ]; do
  case "$1" in
  --swoole-cli-version)
    SWOOLE_CLI_VERSION="$2"
    ;;
  --upload-file)
    UPLOAD_FILE="$2"
    UPLOAD_TYPE='single'
    ;;
  --upload-all)
    UPLOAD_TYPE='all'
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    export NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    PROXY_OPTION="--proxy $2"
    ;;
  --show)
    UPLOAD_TYPE="show"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

mkdir -p ${__PROJECT__}/var/tencent-cloud-object-storage/
cd ${__PROJECT__}/var/tencent-cloud-object-storage/

CLOUD_OBJECT_STORAGE_CONFIG=${__PROJECT__}/var/tencent-cloud-object-storage/.tencent-cloud-object-storage.yaml
if [ ! -f ${CLOUD_OBJECT_STORAGE_CONFIG} ]; then
  cp -f ${__PROJECT__}/sapi/scripts/tencent-cloud-object-storage.yaml ${CLOUD_OBJECT_STORAGE_CONFIG}
  set +u
  if [ -n "${OSS_SECRET_ID}" ] && [ -n "${OSS_SECRET_KEY}" ]; then
    sed -i.bak "s/\${{ secrets.QCLOUD_OSS_SECRET_ID }}/${OSS_SECRET_ID}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
    sed -i.bak "s/\${{ secrets.QCLOUD_OSS_SECRET_KEY }}/${OSS_SECRET_KEY}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
    sed -i.bak "s/\${{ vars.QCLOUD_OSS_BUCKET }}/${OSS_BUCKET}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
    sed -i.bak "s/\${{ vars.QCLOUD_OSS_REGION }}/${OSS_REGION}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
  fi
  set -u
fi

if [ "${OS}" == 'windows' ]; then
  APP_RUNTIME+=".exe"
fi

test -f ${APP_RUNTIME} || curl -fSLo ${APP_RUNTIME} https://github.com/tencentyun/coscli/releases/download/${APP_VERSION}/${APP_RUNTIME}
chmod a+x ${APP_RUNTIME}

BUCKET_NAME=$(grep "\- name: " ${CLOUD_OBJECT_STORAGE_CONFIG} | sed 's/\- name: //g' | sed 's/^ *//;s/ *$//' | tr -d '"')
COSCLI="${__PROJECT__}/var/tencent-cloud-object-storage/${APP_RUNTIME} --config-path ${CLOUD_OBJECT_STORAGE_CONFIG}"
COS_BUCKET_FOLDER="cos://${BUCKET_NAME}/dist/"

if [ "${UPLOAD_TYPE}" == 'all' ]; then
  if [ ! -d ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION} ]; then
    bash ${__PROJECT__}/sapi/scripts/generate-artifact-hash.sh --version ${SWOOLE_CLI_VERSION} ${PROXY_OPTION}
  fi
fi

set +u
if [ -n "$HTTP_PROXY" ] || [ -n "$HTTPS_PROXY" ]; then
  unset $HTTP_PROXY
  unset $HTTPS_PROXY
fi
set -u

if [ "${UPLOAD_TYPE}" == 'all' ]; then
  SWOOLE_VERSION=$(echo ${SWOOLE_CLI_VERSION} | awk -F '.' '{ printf "%s.%s.%s" ,$1,$2,$3 }')
  cd ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION}
  {
    ${COSCLI} cp --forbid-overwrite swoole-cli-${SWOOLE_VERSION}-cygwin-x64.zip ${COS_BUCKET_FOLDER}
    ${COSCLI} cp --forbid-overwrite swoole-cli-${SWOOLE_VERSION}-linux-arm64.tar.xz ${COS_BUCKET_FOLDER}
    ${COSCLI} cp --forbid-overwrite swoole-cli-${SWOOLE_VERSION}-linux-x64.tar.xz ${COS_BUCKET_FOLDER}
    ${COSCLI} cp --forbid-overwrite swoole-cli-${SWOOLE_VERSION}-macos-arm64.tar.xz ${COS_BUCKET_FOLDER}
    ${COSCLI} cp --forbid-overwrite swoole-cli-${SWOOLE_VERSION}-macos-x64.tar.xz ${COS_BUCKET_FOLDER}
    status=$?
  } || {
    status=$?
  }
  if [[ $status -ne 0 ]]; then
    echo $status
    exit 1
  fi
  cd ${__PROJECT__}
  exit 0
fi

if [ "${UPLOAD_TYPE}" == 'single' ]; then
  ${COSCLI} cp ${UPLOAD_FILE} ${COS_BUCKET_FOLDER}
  status=$?
  if [[ $status -ne 0 ]]; then
    echo $status
    exit 1
  fi
  exit 0
fi

if [ "${UPLOAD_TYPE}" == 'show' ]; then
  # cat ${CLOUD_OBJECT_STORAGE_CONFIG}
  # ${COSCLI} --help
  ${COSCLI} ls ${COS_BUCKET_FOLDER}
  status=$?
  if [[ $status -ne 0 ]]; then
    echo $status
    exit 1
  fi
  exit 0
fi
