#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}

mkdir -p ${__PROJECT__}/var

cd ${__PROJECT__}/var

# https://github.com/docker/docker-install.git


# test -f get-docker.sh || curl -fsSL https://get.docker.com -o get-docker.sh
test -f get-docker.sh || curl -fsSL https://github.com/docker/docker-install/blob/master/install.sh?raw=true -o get-docker.sh

if [ -n "$http_proxy" ] || [ -n "$https_proxy" ] || [ -n "$HTTP_PROXY" ] || [ -n "$HTTPS_PROXY" ]; then
    echo 'Please delete proxy settings !'
    echo 'Execute this script again ！'
    exit 0
fi

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china|ustc)
  sed -i "s@https://mirrors.aliyun.com/docker-ce@https://mirrors.ustc.edu.cn/docker-ce@g" get-docker.sh
  sed -i "s@Aliyun)@china)@g" get-docker.sh
  sh get-docker.sh --mirror china
  exit 0
  ;;
tuna)
  sed -i "s@https://mirrors.aliyun.com/docker-ce@https://mirrors.tuna.tsinghua.edu.cn/docker-ce@g" get-docker.sh
  sed -i "s@Aliyun)@china)@g" get-docker.sh
  sh get-docker.sh --mirror china
  exit 0
  ;;
*)
  sh get-docker.sh
  exit 0
  ;;
esac
