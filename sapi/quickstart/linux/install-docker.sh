#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}

mkdir -p ${__PROJECT__}/var

cd ${__PROJECT__}/var

curl -fsSL https://get.docker.com -o get-docker.sh

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
  sed -i "s@https://mirrors.aliyun.com/docker-ce@https://mirrors.ustc.edu.cn/docker-ce@g" get-docker.sh
  sed -i "s@Aliyun)@china)@g" get-docker.sh
  sh get-docker.sh --mirror china
  exit 0
  ;;
*)
  sh get-docker.sh
  exit 0
  ;;
esac
