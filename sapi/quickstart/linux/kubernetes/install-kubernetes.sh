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

mkdir -p kubernetes

cd kubernetes

ip link
ifconfig -a
cat /sys/class/dmi/id/product_uuid
nc 127.0.0.1 6443


# https://github.com/containernetworking/plugins/tags
CNI_PLUGINS_VERSION="v1.4.0"
ARCH="amd64"
DEST="/opt/cni/bin"
mkdir -p "$DEST"
curl -L "https://github.com/containernetworking/plugins/releases/download/${CNI_PLUGINS_VERSION}/cni-plugins-linux-${ARCH}-${CNI_PLUGINS_VERSION}.tgz" |  tar -C "$DEST" -xz


DOWNLOAD_DIR="/usr/local/bin"
mkdir -p "$DOWNLOAD_DIR"


RELEASE="$(curl -sSL https://dl.k8s.io/release/stable.txt)"
ARCH="amd64"
cd $DOWNLOAD_DIR
curl -L --remote-name-all https://dl.k8s.io/release/${RELEASE}/bin/linux/${ARCH}/{kubeadm,kubelet,kubectl}

# chmod +x {kubeadm,kubelet,kubectl}
chmod a+x  /usr/local/bin/kubeadm
chmod a+x  /usr/local/bin/kubelet
chmod a+x  /usr/local/bin/kubectl

# https://github.com/kubernetes/release/tags
RELEASE_VERSION="v0.16.4"
curl -sSL "https://raw.githubusercontent.com/kubernetes/release/${RELEASE_VERSION}/cmd/krel/templates/latest/kubelet/kubelet.service" | sed "s:/usr/bin:${DOWNLOAD_DIR}:g" |  tee /etc/systemd/system/kubelet.service
mkdir -p /etc/systemd/system/kubelet.service.d
curl -sSL "https://raw.githubusercontent.com/kubernetes/release/${RELEASE_VERSION}/cmd/krel/templates/latest/kubeadm/10-kubeadm.conf" | sed "s:/usr/bin:${DOWNLOAD_DIR}:g" |  tee /etc/systemd/system/kubelet.service.d/10-kubeadm.conf


systemctl enable --now kubelet


