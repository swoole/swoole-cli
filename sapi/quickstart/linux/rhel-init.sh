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

