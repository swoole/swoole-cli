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
    export http_proxy="$2"
    export https_proxy="$2"
    export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
    export NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16,198.18.0.0/15,169.254.0.0/16"
    export NO_PROXY="\${NO_PROXY},127.0.0.1,localhost"
    export NO_PROXY="\${NO_PROXY},.aliyuncs.com,.aliyun.com"
    export NO_PROXY="\${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    shift
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

export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"

php -v

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
mkdir -p "${DOWNLOAD_BOX_DIR}"
mkdir -p "${DOWNLOAD_BOX_DIR}/lib/"
mkdir -p "${DOWNLOAD_BOX_DIR}/ext/"


cd "${__PROJECT__}/"

cp -rf pool/* "${DOWNLOAD_BOX_DIR}"


php prepare.php  +ds +inotify +apcu  +pgsql  \
--with-swoole-pgsql=1 \
--with-libavif=1 \
--without-docker=1 --with-skip-download=1 \
--with-dependency-graph=1


cd "${__PROJECT__}/"
# 生成扩展依赖图
bash sapi/extension-dependency-graph/generate-dependency-graph.sh

exit 0

cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-aria2.sh

cd ${__PROJECT__}
bash sapi/download-box/download-dependencies-use-git.sh




