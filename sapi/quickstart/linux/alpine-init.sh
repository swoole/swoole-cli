#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

# use china mirror
# bash sapi/quickstart/linux/alpine-init.sh --mirror [china | ustc | tuna | aliyuncs | tencentyun | huaweicloud]


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
china | tuna | ustc)
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  test "$MIRROR" = "china" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  test "$MIRROR" = "tuna"  && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  test "$MIRROR" = "ustc"  && sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories
  ;;
aliyuncs | tencentyun | huaweicloud) # 云服务的内网镜像源
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  test "$MIRROR" = "aliyuncs" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.cloud.aliyuncs.com/g' /etc/apk/repositories
  test "$MIRROR" = "tencentyun" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tencentyun.com/g' /etc/apk/repositories
  test "$MIRROR" = "huaweicloud" && sed -i 's/dl-cdn.alpinelinux.org/repo.huaweicloud.com/g' /etc/apk/repositories
  ;;

esac

apk update

apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c gettext coreutils gcc g++

apk add bash zip unzip flex pkgconf ca-certificates
apk add tar gzip zip unzip bzip2

apk add bash 7zip
# for alpine 3.17
# apk add bash p7zip
apk add wget git curl
apk add libc++-static libltdl-static

