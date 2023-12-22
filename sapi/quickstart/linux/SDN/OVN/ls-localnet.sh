#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

ovn-nbctl --if-exists ls-del sw_01
ovn-nbctl ls-add sw_01


ovn-nbctl lsp-add sw_01 sw_01_port_04
ovn-nbctl lsp-set-addresses sw_01_port_04 'aa:01:00:00:00:04 192.168.20.4'
ovn-nbctl lsp-set-port-security sw_01_port_04  'aa:01:00:00:00:04 192.168.20.4'

ovn-nbctl lsp-add sw_01 sw_01_port_05
ovn-nbctl lsp-set-addresses sw_01_port_05 'aa:01:00:00:00:05 192.168.20.5'
ovn-nbctl lsp-set-port-security sw_01_port_05  'aa:01:00:00:00:05 192.168.20.5'


ovn-nbctl lsp-add sw_01 sw_01-extranal-port_01
ovn-nbctl lsp-set-type sw_01-extranal-port_01 localnet
ovn-nbctl lsp-set-addresses sw_01-extranal-port_01 unknown
ovn-nbctl lsp-set-options sw_01-extranal-port_01 network_name=external-network-provider

ovn-nbctl lsp-add sw_01 sw_01-extranal-port_02
ovn-nbctl lsp-set-type sw_01-extranal-port_02 localnet
ovn-nbctl lsp-set-addresses sw_01-extranal-port_02 unknown
ovn-nbctl lsp-set-options sw_01-extranal-port_02 network_name=external-network-provider

# 不要轻易用，使用
ovn-sbctl --may-exist lsp-bind sw_01-extranal-port_01 "e2855875-bc0a-4edb-a25f-6f694eea75a9"
ovn-sbctl --may-exist lsp-bind sw_01-extranal-port_02 "12133eef-ec5d-4e44-b859-57129b60c4e5"

ovn-nbctl lsp-list sw_01

ovn-nbctl list logical_switch
ovn-nbctl list logical_switch_port


# ip addr add 192.168.20.243/24 dev br-eth0
# ip addr add 192.168.20.244/24 dev br-eth0


# ip addr add 192.168.20.100/24 dev br-eth0

# iptables -t nat -A POSTROUTING -s 192.168.20.0/24 -o br-eth0 -j MASQUERADE

# tcpdump -i genev_sys_6081 -vvnn

# ip route add default via 192.168.20.100

# ip netns exec vm1 ip route add default via 192.168.20.100

# mkdir -p /etc/netns/vm1/
# cp /etc/resolv.conf /etc/netns/vm1/

# sysctl -w net.ipv4.ip_forward=1

# sysctl -p /etc/sysctl.conf

# sysctl net.ipv4.ip_forward
# cat  /proc/sys/net/ipv4/ip_forward

# ip netns exec vm1 ip link set dev vm1  mtu 1400
# ip netns exec vm1 curl  https://detect-ip.xiaoshuogeng.com/ip/json | jq


# port 6081

# ovs-appctl ofproto/trace br-int \
  #    in_port=3,dl_src=aa:01:00:00:00:04,dl_dst=aa:01:00:00:00:05 -generate

# 广播
# ovs-appctl ofproto/trace br-int \
#    in_port=3,dl_src=00:00:00:00:00:01,dl_dst=ff:ff:ff:ff:ff:ff -generate
