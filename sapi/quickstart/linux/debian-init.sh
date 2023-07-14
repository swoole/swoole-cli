#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

mirror=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    mirror="$2"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$mirror" in
china)
  OS_ID=$(cat /etc/os-release | grep '^ID=' | awk -F '=' '{print $2}')
  VERSION_ID=$(cat /etc/os-release | grep '^VERSION_ID=' | awk -F '=' '{print $2}' | sed "s/\"//g")
  case $OS_ID in
  debian)
    case $VERSION_ID in
    11)
      test -f /etc/apt/sources.list.save || cp /etc/apt/sources.list /etc/apt/sources.list.save
      sed -i "s@deb.debian.org@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
      sed -i "s@security.debian.org@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
      ;;
    12)
      test -f /etc/apt//etc/apt/sources.list.d/debian.sources.save || cp /etc/apt/sources.list.d/debian.sources /etc/apt/sources.list.d/debian.sources.save
      sed -i 's/deb.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list.d/debian.sources
      ;;
    *)
      echo 'no match debian os version' . $VERSION_ID
      ;;
    esac
    ;;
  ubuntu)
    case $VERSION_ID in
    20.04 | 22.04 | 22.10 | 23.04 | 23.10)
      test -f /etc/apt/sources.list.save || cp /etc/apt/sources.list /etc/apt/sources.list.save
      sed -i "s@security.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
      sed -i "s@archive.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
      ;;
    *)
      echo 'no match ubuntu os version' . $VERSION_ID
      ;;
    esac
    ;;
  *)
    echo 'NO SUPPORT LINUX OS'
    exit 0
    ;;
  esac

  ;;

esac

apt update -y
apt install -y git curl wget ca-certificates
apt install -y xz-utils autoconf automake clang-tools clang lld libtool cmake bison re2c gettext coreutils lzip zip unzip
apt install -y pkg-config bzip2 flex p7zip
apt install -y gcc g++

# apt install build-essential linux-headers-$(uname -r)
