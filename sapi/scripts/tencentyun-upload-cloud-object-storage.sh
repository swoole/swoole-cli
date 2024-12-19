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

SWOOLE_CLI_VERSION='v5.1.6.0'
SWOOLE_VERSION='v5.1.6'
UPLOAD_FILE=''
UPLOAD_TYPE=''
while [ $# -gt 0 ]; do
  case "$1" in
  --swoole-cli-version)
    SWOOLE_CLI_VERSION="$2"
    ;;
  --upload-single-file)
    UPLOAD_FILE="$2"
    UPLOAD_TYPE='single'
    ;;
  --upload-all)
    UPLOAD_TYPE='all'
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# ${__PROJECT__}/var/upload-release-oss/coscli --help

CLOUD_OBJECT_STORAGE_CONFIG=${__PROJECT__}/var/upload-release-oss/.tencentyun-cloud-object-storage.yaml
if [ ! -f ${CLOUD_OBJECT_STORAGE_CONFIG} ]; then
  cp -f ${__PROJECT__}/sapi/scripts/tencentyun-cloud-object-storage.yaml ${CLOUD_OBJECT_STORAGE_CONFIG}
  if [ -n "${SECRET_ID}" ] && [ -n "${SECRET_KEY}" ]; then
    sed -i.bak "s/\${{ secrets.QCLOUD_OSS_SECRET_ID }}/${SECRET_ID}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
    sed -i.bak "s/\${{ secrets.QCLOUD_OSS_SECRET_KEY }}/${SECRET_KEY}/" ${CLOUD_OBJECT_STORAGE_CONFIG}
  fi
fi

COSCLI="${__PROJECT__}/var/upload-release-oss/coscli --config-path ${CLOUD_OBJECT_STORAGE_CONFIG} "
COS_BUCKET_FOLDER="cos://wenda-1257035567/dist/"

${COSCLI}  ls ${COS_BUCKET_FOLDER}

if [ "${UPLOAD_TYPE}" = 'single' ]; then
  ${COSCLI} sync ${UPLOAD_FILE} ${COS_BUCKET_FOLDER}
  exit 0
fi

if [ "${UPLOAD_TYPE}" = 'all' ]; then
  if [ -d ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION} ]; then
    SWOOLE_VERSION=$(echo ${SWOOLE_CLI_VERSION} | awk -F '.' '{ printf "%s.%s.%s" ,$1,$2,$3 }')
  else
    echo "please download release artifact and upload !"
    echo "bash ${__PROJECT__}/sapi/scripts/generate-artifact-hash.sh --version ${SWOOLE_CLI_VERSION}"
    echo "bash ${__PROJECT__}/sapi/scripts/tencentyun-upload-cloud-object-storage.sh --swoole-cli-version ${SWOOLE_CLI_VERSION} --upload-all"
    exit 0
  fi

  cd ${__PROJECT__}/var/artifact-hash/${SWOOLE_CLI_VERSION}
  ${COSCLI} sync swoole-cli-${SWOOLE_VERSION}-cygwin-x64.zip ${COS_BUCKET_FOLDER}
  ${COSCLI} sync swoole-cli-${SWOOLE_VERSION}-linux-arm64.tar.xz ${COS_BUCKET_FOLDER}
  ${COSCLI} sync swoole-cli-${SWOOLE_VERSION}-linux-x64.tar.xz ${COS_BUCKET_FOLDER}
  ${COSCLI} sync swoole-cli-${SWOOLE_VERSION}-macos-arm64.tar.xz ${COS_BUCKET_FOLDER}
  ${COSCLI} sync swoole-cli-${SWOOLE_VERSION}-macos-x64.tar.xz ${COS_BUCKET_FOLDER}

  cd ${__PROJECT__}
  exit 0
fi
