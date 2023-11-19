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

mkdir -p containerd

cd containerd


stat -fc %T /sys/fs/cgroup/

# containerd
VERSION=1.7.9

CONTAINERD_RELEASE_URL=https://github.com/containerd/containerd/releases/download/v1.7.9/containerd-1.7.9-linux-amd64.tar.gz
CONTAINERD_RELEASE=containerd-1.7.9-linux-amd64.tar.gz

curl  -L -o $CONTAINERD_RELEASE  $CONTAINERD_RELEASE_URL

tar Cxzvf /usr/local containerd-1.7.9-linux-amd64.tar.gz



mkdir -p /usr/local/lib/systemd/system/
curl  -L -o /usr/local/lib/systemd/system/containerd.service  https://raw.githubusercontent.com/containerd/containerd/main/containerd.service

systemctl daemon-reload
systemctl enable --now containerd
systemctl restart containerd

mkdir -p /etc/containerd/
containerd config default > /etc/containerd/config.toml

sed -i 's/SystemdCgroup = false/SystemdCgroup = true/' /etc/containerd/config.toml
sed -i 's/ disabled_plugins = []/ /' /etc/containerd/config.toml
cat /etc/containerd/config.toml



# runc
curl  -L -o runc.amd64 https://github.com/opencontainers/runc/releases/download/v1.1.10/runc.amd64

install -m 755 runc.amd64 /usr/local/sbin/runc




# kubernetes 需要如下配置

# cni-plugins

mkdir -p /opt/cni/bin
curl  -L -o cni-plugins-linux-amd64-v1.3.0.tgz https://github.com/containernetworking/plugins/releases/download/v1.3.0/cni-plugins-linux-amd64-v1.3.0.tgz
tar Cxzvf /opt/cni/bin cni-plugins-linux-amd64-v1.3.0.tgz


# crictl

VERSION="v1.28.0" # check latest version in /releases page
curl  -L -O https://github.com/kubernetes-sigs/cri-tools/releases/download/$VERSION/crictl-$VERSION-linux-amd64.tar.gz
tar zxvf crictl-$VERSION-linux-amd64.tar.gz -C /usr/local/bin
rm -f crictl-$VERSION-linux-amd64.tar.gz

cat > /etc/crictl.yaml <<EOF
runtime-endpoint: unix:///var/run/containerd/containerd.sock
image-endpoint: unix:///var/run/containerd/containerd.sock
timeout: 10
#debug: true
EOF



