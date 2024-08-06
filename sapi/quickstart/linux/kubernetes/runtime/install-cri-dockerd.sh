#!/bin/bash
set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn"
    NO_PROXY="${NO_PROXY},.tencent.com"
    NO_PROXY="${NO_PROXY},.sourceforge.net"
    NO_PROXY="${NO_PROXY},.npmmirror.com"
    export NO_PROXY="${NO_PROXY}"
    ;;

  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# shellcheck disable=SC2034
OS=$(uname -s)
# shellcheck disable=SC2034
ARCH=$(uname -m)

mkdir -p cri-dockerd/packaging/systemd/

cd cri-dockerd
# cri-dockerd
# https://github.com/Mirantis/cri-dockerd/tags

VERSION="0.3.14"
curl -L -O https://github.com/Mirantis/cri-dockerd/releases/download/v${VERSION}/cri-dockerd-${VERSION}.amd64.tgz

tar --strip-components=1 -C . -xf  cri-dockerd-${VERSION}.amd64.tgz


curl -L -o packaging/systemd/cri-docker.service https://github.com/Mirantis/cri-dockerd/blob/master/packaging/systemd/cri-docker.service?raw=true
curl -L -o packaging/systemd/cri-docker.socket https://github.com/Mirantis/cri-dockerd/blob/master/packaging/systemd/cri-docker.socket?raw=true


mkdir -p /usr/local/bin
install -o root -g root -m 0755 cri-dockerd /usr/local/bin/cri-dockerd
install packaging/systemd/* /etc/systemd/system
sed -i -e 's,/usr/bin/cri-dockerd,/usr/local/bin/cri-dockerd,' /etc/systemd/system/cri-docker.service

systemctl daemon-reload
systemctl enable --now cri-docker.socket
systemctl status cri-docker.service | cat

# crictl
# check latest version in /releases page
# https://github.com/kubernetes-sigs/cri-tools/tags

VERSION="1.30.0"
curl  -L -O https://github.com/kubernetes-sigs/cri-tools/releases/download/v${VERSION}/crictl-v${VERSION}-linux-amd64.tar.gz
tar zxvf crictl-v${VERSION}-linux-amd64.tar.gz -C /usr/local/bin
rm -f crictl-v${VERSION}-linux-amd64.tar.gz

cat > /etc/crictl.yaml <<EOF
runtime-endpoint: unix:///var/run/cri-dockerd.sock
image-endpoint: unix:///var/run/cri-dockerd.sock
timeout: 10
# debug: true
EOF



