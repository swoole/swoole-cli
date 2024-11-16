#!/usr/bin/env sh

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

# use china mirror
# sh sapi/quickstart/linux/alpine-init.sh --mirror [ china | ustc | tuna | tencentyun | huaweicloud ]

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
  test "$MIRROR" = "tuna" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  test "$MIRROR" = "ustc" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories
  ;;
tencentyun | huaweicloud) # 云服务的内网镜像源
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  test "$MIRROR" = "tencentyun" && sed -i 's/dl-cdn.alpinelinux.org/mirrors.tencentyun.com/g' /etc/apk/repositories
  test "$MIRROR" = "huaweicloud" && sed -i 's/dl-cdn.alpinelinux.org/repo.huaweicloud.com/g' /etc/apk/repositories
  ;;

esac

apk update

apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c coreutils gcc g++
apk add bash zip unzip flex pkgconf ca-certificates
apk add tar gzip zip unzip bzip2 gettext gettext-dev

apk add bash 7zip
# apk add bash p7zip

apk add wget git curl
apk add libc++-static libltdl-static
apk add yasm nasm
apk add ninja python3 py3-pip
apk add diffutils
apk add netcat-openbsd socat
apk add python3-dev
apk add mercurial
apk add pigz parallel

case "$MIRROR" in
china | tuna | ustc)
  pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
  test "$MIRROR" = "ustc" && pip3 config set global.index-url https://mirrors.ustc.edu.cn/pypi/web/simple
  ;;
tencentyun | huaweicloud)
  test "$MIRROR" = "tencentyun" && pip3 config set global.index-url https://mirrors.tencentyun.com/pypi/simple/
  test "$MIRROR" = "huaweicloud" && pip3 config set global.index-url https://repo.huaweicloud.com/pypi/simple/
  ;;
esac

# pip3 install meson
apk add meson

