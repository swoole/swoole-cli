#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}



OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release |tr -d '\n' | tr -d '\"')

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

# mirror site
# https://mirror.rockylinux.org/mirrormanager/mirrors
# https://mirrors.almalinux.org/


case "$MIRROR" in
china)
    case "$OS_RELEASE" in
      almalinux)
        sed -e 's|^mirrorlist=|#mirrorlist=|g' \
            -e 's|^# baseurl=https://repo.almalinux.org|baseurl=https://mirror.sjtu.edu.cn|g' \
            -i.bak \
            /etc/yum.repos.d/almalinux*.repo
        ;;
      rocky)
        sed -e 's|^mirrorlist=|#mirrorlist=|g' \
            -e 's|^#baseurl=http://dl.rockylinux.org/$contentdir|baseurl=https://mirrors.ustc.edu.cn/rocky|g' \
            -i.bak \
            /etc/yum.repos.d/rocky*.repo
        ;;

    esac
    ;;
  aliyun)
    case "$OS_RELEASE" in
      almalinux)
        sed -e 's|^mirrorlist=|#mirrorlist=|g' \
            -e 's|^# baseurl=https://repo.almalinux.org|baseurl=https://mirrors.aliyun.com|g' \
            -i.bak \
            /etc/yum.repos.d/almalinux*.repo
        ;;
      rocky)
        sed -e 's|^mirrorlist=|#mirrorlist=|g' \
            -e 's|^#baseurl=http://dl.rockylinux.org/$contentdir|baseurl=https://mirrors.aliyun.com/rockylinux|g' \
            -i.bak \
            /etc/yum.repos.d/rocky*.repo
        ;;

    esac
    ;;
  *)
    echo 'no use mirror'
    ;;
esac


dnf makecache



yum install -y git  wget ca-certificates

yum install -y autoconf automake  clang lld libtool cmake bison  gettext   zip unzip
yum install -y pkg-config bzip2 flex


yum install -y curl-minimal
yum install -y xz
yum install -y socat

exit 0
yum install -y clang-tools re2c lzip  coreutils p7zip
