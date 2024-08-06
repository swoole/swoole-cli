#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

# use china mirror
# sh sapi/quickstart/linux/alpine-init-mini.sh --mirror [ china | ustc | tuna | tencentyun | huaweicloud ]


MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --*)
    echo "no found mirror option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china | tuna | ustc)
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  test "$MIRROR" = "china" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  test "$MIRROR" = "tuna"  && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  test "$MIRROR" = "ustc"  && sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories
  ;;
tencentyun | huaweicloud) # 云服务的内网镜像源
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  test "$MIRROR" = "tencentyun" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tencentyun.com/g' /etc/apk/repositories
  test "$MIRROR" = "huaweicloud" && sed -i 's/dl-cdn.alpinelinux.org/repo.huaweicloud.com/g' /etc/apk/repositories
  ;;

esac

apk update

apk add bash git curl wget  xz zip unzip  ca-certificates
