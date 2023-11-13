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

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com,.tencent.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


cd ${__PROJECT__}/


if [ ! -f ${__PROJECT__}/build-release.sh ] ; then
    echo 'please script:'
    echo "bash ${__PROJECT__}/build-release.sh --mirror china  --download-box"
    exit 0
fi


DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/
mkdir -p lib
mkdir -p ext

cd ${__PROJECT__}

# 生成扩展依赖图
sh sapi/scripts/generate-dependency-graph.sh

# 准备源码
## 兼容macos 需要的源码包 或者 跳过批量下载

awk 'BEGIN { cmd="cp -ri pool/lib/* var/download-box/lib/ "  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri pool/ext/* var/download-box/ext/ "  ; print "n" |cmd; }'


