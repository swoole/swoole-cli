#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

ovn-nbctl --if-exists ls-del sw_01
ovn-nbctl ls-add sw_01


ovn-nbctl lsp-add sw_01 sw_01-extranal-port
ovn-nbctl lsp-set-type sw_01-extranal-port l2gateway
ovn-nbctl lsp-set-addresses sw_01-extranal-port unknown
ovn-nbctl lsp-set-options sw_01-extranal-port network_name=external-network-provider l2gateway-chassis="edde411e-f3ed-41e7-b98c-1ba87da462fc"




ovn-nbctl lsp-list sw_01

# ip addr add 192.168.20.100/24 dev br-eth0

# iptables -t nat -A POSTROUTING -s 192.168.20.0/24 -o br-eth0 -j MASQUERADE

# tcpdump -i genev_sys_6081 -vvnn

# ip route add default via 192.168.20.100

# ip netns exec vm1 ip route add default via 192.168.20.100

# mkdir -p /etc/netns/vm1/
# cp /etc/resolv.conf /etc/netns/vm1/

# sysctl -w net.ipv4.ip_forward=1

# sysctl -p /etc/sysctl.conf

# ip netns exec vm1 ip link set dev vm1  mtu 1400
# ip netns exec vm1 curl  https://detect-ip.xiaoshuogeng.com/ip/json | jq