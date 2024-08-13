#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}/sapi/quickstart/linux/

bash debian-init-minimal.sh "$@"



apt install -y sudo wget curl libssl-dev ca-certificates
apt install -y net-tools iproute2
apt install -y ipvsadm conntrack iptables ebtables ethtool socat
apt install -y python3 python3-pip
apt install -y xz-utils  lzip zip unzip p7zip
apt install -y nftables


ip link
ifconfig -a
cat /sys/class/dmi/id/product_uuid
# nc 127.0.0.1 6443
stat -fc %T /sys/fs/cgroup/
