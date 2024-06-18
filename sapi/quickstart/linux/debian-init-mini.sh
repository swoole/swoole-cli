#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

# use china mirror
# bash sapi/quickstart/linux/debian-init-mini.sh --mirror [ china | ustc | tuna | aliyuncs | tencentyun | huaweicloud ]

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    case "$2" in
    china | ustc | tuna | aliyuncs | tencentyun | huaweicloud )
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



if test -n "$MIRROR" ; then
{
  OS_ID=$(cat /etc/os-release | grep '^ID=' | awk -F '=' '{print $2}')
  VERSION_ID=$(cat /etc/os-release | grep '^VERSION_ID=' | awk -F '=' '{print $2}' | sed "s/\"//g")
  case $OS_ID in
  debian)
    case $VERSION_ID in
    11 | 12 )
      # debian 容器内和容器外 镜像源配置不一样
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

apt update -y

apt install -y git curl wget ca-certificates xz-utils bzip2 p7zip lzip zip unzip
