#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

# use china mirror
# bash sapi/quickstart/linux/debian-init.sh --mirror [ china | ustc | tuna | aliyuncs | tencentyun | huaweicloud ]

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    case "$2" in
    china | ustc | tuna | aliyuncs | tencentyun | huaweicloud)
      MIRROR="$2"
      ;;
    esac
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

if test -n "$MIRROR"; then
  {
    OS_ID=$(cat /etc/os-release | grep '^ID=' | awk -F '=' '{print $2}')
    VERSION_ID=$(cat /etc/os-release | grep '^VERSION_ID=' | awk -F '=' '{print $2}' | sed "s/\"//g")
    case $OS_ID in
    debian)
      case $VERSION_ID in
      11 | 12)
        # 容器内和容器外 镜像源配置不一样
        if [ -f /.dockerenv ] && [ "$VERSION_ID" = 12 ]; then
          test -f /etc/apt/sources.list.d/debian.sources.save || cp -f /etc/apt/sources.list.d/debian.sources /etc/apt/sources.list.d/debian.sources.save
          sed -i 's/deb.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list.d/debian.sources
          sed -i 's/security.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list.d/debian.sources
          test "$MIRROR" = "tuna" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list.d/debian.sources
          # 云服务内网镜像源
          test "$MIRROR" = "aliyuncs" && sed -i "s@mirrors.ustc.edu.cn@mirrors.cloud.aliyuncs.com@g" /etc/apt/sources.list.d/debian.sources
          test "$MIRROR" = "tencentyun" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tencentyun.com@g" /etc/apt/sources.list.d/debian.sources
          test "$MIRROR" = "huaweicloud" && sed -i "s@mirrors.ustc.edu.cn@repo.huaweicloud.com@g" /etc/apt/sources.list.d/debian.sources
        else
          test -f /etc/apt/sources.list.save || cp /etc/apt/sources.list /etc/apt/sources.list.save
          sed -i "s@deb.debian.org@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
          sed -i "s@security.debian.org@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
          test "$MIRROR" = "tuna" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list
          test "$MIRROR" = "aliyuncs" && sed -i "s@mirrors.ustc.edu.cn@mirrors.cloud.aliyuncs.com@g" /etc/apt/sources.list
          test "$MIRROR" = "tencentyun" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tencentyun.com@g" /etc/apt/sources.list
          test "$MIRROR" = "huaweicloud" && sed -i "s@mirrors.ustc.edu.cn@repo.huaweicloud.com@g" /etc/apt/sources.list
        fi
        ;;
      *)
        echo 'no match debian OS version' . $VERSION_ID
        ;;
      esac
      ;;
    ubuntu)
      case $VERSION_ID in
      20.04 | 22.04 | 22.10 | 23.04 | 23.10)
        test -f /etc/apt/sources.list.save || cp /etc/apt/sources.list /etc/apt/sources.list.save
        sed -i "s@security.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
        sed -i "s@archive.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list
        test "$MIRROR" = "tuna" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list
        test "$MIRROR" = "aliyuncs" && sed -i "s@mirrors.ustc.edu.cn@mirrors.cloud.aliyuncs.com@g" /etc/apt/sources.list
        test "$MIRROR" = "tencentyun" && sed -i "s@mirrors.ustc.edu.cn@mirrors.tencentyun.com@g" /etc/apt/sources.list
        test "$MIRROR" = "huaweicloud" && sed -i "s@mirrors.ustc.edu.cn@repo.huaweicloud.com@g" /etc/apt/sources.list
        ;;
      24.04)
        test -f /etc/apt/sources.list.d/ubuntu.sources.save || cp /etc/apt/sources.list.d/ubuntu.sources /etc/apt/sources.list.d/ubuntu.sources.save
        sed -i "s@security.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list.d/ubuntu.sources
        sed -i "s@archive.ubuntu.com@mirrors.ustc.edu.cn@g" /etc/apt/sources.list.d/ubuntu.sources
        ;;
      *)
        echo 'no match ubuntu OS version' . $VERSION_ID
        ;;
      esac
      ;;
    *)
      echo 'NO SUPPORT LINUX OS'
      exit 0
      ;;
    esac
  }
fi

test -f /etc/apt/apt.conf.d/proxy.conf && rm -rf /etc/apt/apt.conf.d/proxy.conf

export DEBIAN_FRONTEND=noninteractive

export TZ="UTC"
export TZ="Etc/UTC"
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ >/etc/timezone

apt update -y
apt install -y locales
locale-gen en_US.UTF-8
# dpkg-reconfigure locales
# localedef -v -c -i en_US -f UTF-8 en_US.UTF-8
# update-locale
localedef -v -c -i en_US -f UTF-8 en_US.UTF-8

export LANGUAGE="en_US.UTF-8"
export LC_ALL="en_US.UTF-8"
export LC_CTYPE="en_US.UTF-8"
export LANG="en_US.UTF-8"

apt install -y git curl wget ca-certificates
apt install -y xz-utils autoconf automake clang-tools clang lld libtool cmake bison re2c gettext coreutils lzip zip unzip
apt install -y pkg-config bzip2 flex p7zip
apt install -y gcc g++ libtool-bin autopoint
apt install -y linux-headers-generic
apt install -y musl-dev musl-tools

# apt install -y linux-headers-$(uname -r)

# apt install build-essential linux-headers-$(uname -r)
apt install -y python3 python3-pip ninja-build diffutils

apt install -y yasm nasm
apt install -y meson
apt install -y netcat-openbsd socat

case "$MIRROR" in
china | tuna | ustc)
  pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
  test "$MIRROR" = "ustc" && pip3 config set global.index-url https://mirrors.ustc.edu.cn/pypi/web/simple
  ;;
aliyuncs | tencentyun | huaweicloud)
  test "$MIRROR" = "aliyuncs" && pip3 config set global.index-url https://mirrors.cloud.aliyuncs.com/pypi/simple/
  test "$MIRROR" = "tencentyun" && pip3 config set global.index-url https://mirrors.tencentyun.com/pypi/simple/
  test "$MIRROR" = "huaweicloud" && pip3 config set global.index-url https://repo.huaweicloud.com/pypi/simple/
  ;;
esac
