#!/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl --if-exists del-br br-eth1
ovs-vsctl add-br br-eth1 # 添加网桥
ip link set br-eth1 up # 激活网桥

{

    ip addr add  172.16.124.60/20 dev br-eth1

    # 自定义私网地址
    ip addr add  10.4.20.2/24 dev br-eth1

    # ip route add <DESTINATION> via <GATEWAY> <dev> INTERFACE
    ip route add 10.1.0.0/24 via 10.4.20.1 dev br-eth1


} ||
{
    echo $?
}

ovs-vsctl add-port br-eth1 eth1
ovs-vsctl set Open_vSwitch . external-ids:ovn-bridge-mappings=external-network-provider:br-eth1

ip addr flush dev eth1

sysctl -w net.ipv4.ip_forward=1
# iptables -t nat -A POSTROUTING -s 10.1.20.0/24 -o br-eth0 -j MASQUERADE

ip a

exit 0
