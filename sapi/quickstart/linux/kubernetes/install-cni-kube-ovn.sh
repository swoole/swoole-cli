#!/usr/bin/env bash

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    export NO_PROXY="${NO_PROXY}"
    ;;

  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# 从 Kube-OVN v1.12.0 版本开始，支持 Helm Chart 安装，默认部署为 Overlay 类型网络。
# CNI kube-ovn
# https://github.com/kubeovn/kube-ovn?tab=readme-ov-file
# https://kubeovn.github.io/docs/stable/start/one-step-install/
# https://github.com/kubeovn/kube-ovn/tags
VERSION="release-1.12"

curl -fSLo kube-ovn-${VERSION}-install.sh https://raw.githubusercontent.com/kubeovn/kube-ovn/${VERSION}/dist/images/install.sh

bash kube-ovn-${VERSION}-install.sh

# 卸载 和 清理残余
curl -fSLo kube-ovn-${VERSION}-uninstall.sh https://raw.githubusercontent.com/kubeovn/kube-ovn/${VERSION}/dist/images/cleanup.sh

cat >kube-ovn-${VERSION}-clean.sh <<EOF
rm -rf /var/run/openvswitch
rm -rf /var/run/ovn
rm -rf /etc/origin/openvswitch/
rm -rf /etc/origin/ovn/
rm -rf /etc/cni/net.d/00-kube-ovn.conflist
rm -rf /etc/cni/net.d/01-kube-ovn.conflist
rm -rf /var/log/openvswitch
rm -rf /var/log/ovn
rm -fr /var/log/kube-ovn

# reboot

EOF
