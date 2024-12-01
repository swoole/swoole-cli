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

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn"
    NO_PROXY="${NO_PROXY},.tencent.com"
    NO_PROXY="${NO_PROXY},.sourceforge.net"
    export NO_PROXY="${NO_PROXY},.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/
mkdir -p lib
mkdir -p ext

if [ -f download_library_urls.txt ] && [ -f download_extension_urls.txt ]; then
  echo 'downloading source code tarball '
else
  echo 'please run script : '
  echo "prepare.php --skip-download=1 --with-dependency-graph=1 --with-swoole-pgsql=1 +apcu +ds +xlswriter +ssh2  "
  exit 0
fi

cd ${__PROJECT__}
bash sapi/download-box/download-box-dependencies-use-aria2.sh

cd ${__PROJECT__}
bash sapi/download-box/download-box-dependencies-use-git.sh
bash sapi/download-box/download-dependencies-use-aria2.sh

# 例子
# bash build-release.sh --mirror china  --download-box
# bash sapi/download-box/download-box-batch-downloader.sh
