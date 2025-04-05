#!/usr/bin/env bash

set -ex
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

if [ -f "${__DIR__}/../../../prepare.php" ]; then
  __PROJECT__=$(
    cd ${__DIR__}/../../../
    pwd
  )
else
  __PROJECT__=${__DIR__}
fi

cd ${__PROJECT__}

mkdir -p ${__PROJECT__}/var

cd ${__PROJECT__}/var

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

# https://github.com/docker/docker-install.git
# test -f get-docker.sh || curl -fsSL https://get.docker.com -o get-docker.sh

case "$MIRROR" in
china | ustc | tuna)
  test -f get-docker.sh || curl -fsSL https://gitee.com/jingjingxyk/docker-install/raw/master/install.sh -o get-docker.sh
  ;;
*)
  test -f get-docker.sh || curl -fsSL https://github.com/docker/docker-install/blob/master/install.sh?raw=true -o get-docker.sh
  ;;
esac

if [ -n "$http_proxy" ] || [ -n "$https_proxy" ] || [ -n "$HTTP_PROXY" ] || [ -n "$HTTPS_PROXY" ]; then
  set +u
  unset http_proxy
  unset https_proxy
  unset HTTP_PROXY
  unset HTTPS_PROXY
  set -u
fi

case "$MIRROR" in
china | ustc)
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
