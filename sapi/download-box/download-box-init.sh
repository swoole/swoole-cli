#!/bin/bash

set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)

cd ${__PROJECT__}/

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


# 请先执行 build-release-example.sh 脚本
#  bash build-release-example.sh

if [ ! -f "${__PROJECT__}/bin/runtime/php" ] ;then
  echo "请准备运行环境"
  echo " 执行 bash build-release-example.sh"
  exit 0
fi


DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
mkdir -p "${DOWNLOAD_BOX_DIR}"
mkdir -p "${DOWNLOAD_BOX_DIR}/lib/"
mkdir -p "${DOWNLOAD_BOX_DIR}/ext/"

cd "${__PROJECT__}/"


# 生成扩展依赖图
bash sapi/extension-dependency-graph/generate-dependency-graph.sh


cd ${__PROJECT__}
bash sapi/download-box/download-box-dependencies-use-aria2.sh

cd ${__PROJECT__}
bash sapi/download-box/download-box-dependencies-use-git.sh


# 例子
# bash build-release-example.sh --mirror china  --download-box
# bash sapi/download-box/download-box-init.sh --proxy http://192.168.3.26:8015
# bash sapi/download-box/download-box-dependencies-sync.sh



