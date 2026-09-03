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

test -d ${__PROJECT__}/var/download-box/ || mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/
SWOOLE_CLI_RELEASE_VERSION="v6.2.0.0"
ALL_DEPS_HASH=""

DOMAIN="https://github.com/swoole/swoole-cli/releases/download/${SWOOLE_CLI_RELEASE_VERSION}/"

# show sha256sum
# curl -fSL https://github.com/swoole/swoole-cli/releases/download/v6.1.1.1/all-deps.zip.sha256sum && echo
# curl -fSL https://storage.swoole.com/dist/all-deps.zip.sha256sum && echo
# download command
# curl -fSLo all-deps.zip https://github.com/swoole/swoole-cli/releases/download/v6.1.1.1/all-deps.zip
# curl -fSLo all-deps.zip https://storage.swoole.com/dist/all-deps.zip

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    if [ "$2" = 'china' ]; then
      DOMAIN='https://storage.swoole.com/dist/'
    fi
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn"
    NO_PROXY="${NO_PROXY},.tencent.com"
    NO_PROXY="${NO_PROXY},ftpmirror.gnu.org"
    NO_PROXY="${NO_PROXY},gitee.com,gitcode.com"
    NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    NO_PROXY="${NO_PROXY},.jingjingxyk.com,jingjingxyk.cn"
    export NO_PROXY="${NO_PROXY},.npmmirror.com"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

URL="${DOMAIN}/all-deps.zip"
HASH_CODE_URL="${DOMAIN}/all-deps.zip.sha256sum "

ALL_DEPS_HASH=$(curl -fSL ${HASH_CODE_URL} && echo)

test -f all-deps.zip || curl -fSLo all-deps.zip ${URL}

# hash 签名
HASH=$(sha256sum all-deps.zip | awk '{print $1}')

# 签名验证失败，删除下载文件
if [ ${HASH} != ${ALL_DEPS_HASH} ]; then
  set +x
  echo 'Hash signature is invalid ！'
  echo 'Download keeps failing. Please pull the latest code !'
  rm -f all-deps.zip
  exit 0
fi

unzip -n all-deps.zip

cd ${__PROJECT__}/

awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'
set +x
echo "download all-archive.zip ok ！"
