#!/bin/bash
set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ip link
ifconfig -a
cat /sys/class/dmi/id/product_uuid
nc 127.0.0.1 6443
stat -fc %T /sys/fs/cgroup/

sudo apt-get update

apt install -y sudo wget curl libssl-dev ca-certificates
apt install -y net-tools iproute2
apt install -y ipvsadm conntrack iptables ebtables ethtool socat


mkdir -p kubernetes

cd kubernetes




sudo apt-get update
# apt-transport-https may be a dummy package; if so, you can skip that package
sudo apt-get install -y apt-transport-https ca-certificates curl gpg

sudo mkdir -m 755 /etc/apt/keyrings

curl -fsSL https://pkgs.k8s.io/core:/stable:/v1.28/deb/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/kubernetes-apt-keyring.gpg

echo 'deb [signed-by=/etc/apt/keyrings/kubernetes-apt-keyring.gpg] https://pkgs.k8s.io/core:/stable:/v1.28/deb/ /' | sudo tee /etc/apt/sources.list.d/kubernetes.list



sudo apt-get update
sudo apt-get install -y kubelet kubeadm kubectl
sudo apt-mark hold kubelet kubeadm kubectl




