#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

# use china mirror
# bash sapi/quickstart/linux/alpine-init.sh --mirror china

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china)
  test -f /etc/apk/repositories.save || cp /etc/apk/repositories /etc/apk/repositories.save
  sed -i 's/dl-cdn.alpinelinux.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apk/repositories
  ;;

esac

apk update

apk add vim alpine-sdk xz autoconf automake linux-headers clang-dev clang lld libtool cmake bison re2c gettext coreutils gcc g++
apk add bash p7zip zip unzip flex pkgconf ca-certificates
# apk add bash 7zip
apk add wget git curl
apk add libc++-static libltdl-static
apk add yasm nasm
apk add ninja python3 py3-pip
apk add diffutils
apk add netcat-openbsd

case "$MIRROR" in
china)
  pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
  ;;

esac

pip3 install meson

