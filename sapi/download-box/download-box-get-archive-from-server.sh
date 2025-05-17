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

mkdir -p pool/lib
mkdir -p pool/ext

mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/

if [ -f "${__PROJECT__}/sapi/PHP-VERSION.conf" ]; then
  DOMAIN='https://github.com/swoole/swoole-cli/releases/download/v6.0.0.0/'
  ALL_DEPS_HASH="a55699ecee994032f33266dfa37eabb49f1f6d6b6b65cdcf7b881cac09c63bea"
else
  # show sha256sum
  # curl -LS https://github.com/swoole/build-static-php/releases/download/v1.10.0/all-deps.zip.sha256sum | cat
  DOMAIN='https://github.com/swoole/build-static-php/releases/download/v1.10.0/'
  ALL_DEPS_HASH="62681e789de493c6aee51c9b5ad7ee9c37684ac6b2bb867e086f6ca9c70bca44"
fi

DOWNLOAD_ARCHIVE=0
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    if [ "$2" = 'china' ]; then
      DOMAIN='https://swoole-cli.jingjingxyk.com/'
      if [ ! -f "${__PROJECT__}/sapi/PHP-VERSION.conf" ]; then
        DOMAIN='https://php-cli.jingjingxyk.com/'
      fi
    fi
    ;;
  --download-archive)
    DOWNLOAD_ARCHIVE=1
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
    NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

URL="${DOMAIN}/all-deps.zip"

test -f all-deps.zip || curl -fSLo all-deps.zip ${URL}

# https://www.runoob.com/linux/linux-comm-unzip.html
# -o 不必先询问用户，unzip执行后覆盖原有文件。
# -n 解压缩时不要覆盖原有的文件。

# hash 签名
HASH=$(sha256sum all-deps.zip | awk '{print $1}')

# 签名验证失败，删除下载文件
if [ ${HASH} != ${ALL_DEPS_HASH} ]; then
  echo 'hash signature is invalid ！'
  rm -f all-deps.zip
  echo '                       '
  echo ' Please Download Again '
  echo '                       '
  exit 0
fi

if [ $DOWNLOAD_ARCHIVE -eq 1 ]; then
  cp -rf all-deps.zip ${__PROJECT__}/pool/
  cd ${__PROJECT__}/
else
  unzip -n all-deps.zip
  cd ${__PROJECT__}/

  awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
  awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'
fi

echo "download all-archive.zip ok ！"
