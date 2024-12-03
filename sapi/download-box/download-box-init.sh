#!/usr/bin/env bash

set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)

cd ${__PROJECT__}

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
