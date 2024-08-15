#!/usr/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl --if-exists del-port   br-int ovn0
ovs-vsctl --may-exist add-port br-int ovn0 -- set interface ovn0 type=internal

ip link set ovn0 up # 激活网桥

{
    ip addr add  10.4.20.2/24 dev ovn0
    ip route add 10.4.20.0/24 via 10.4.20.1 dev ovn0

} ||
{
    echo $?
}

# ovs-vsctl set Open_vSwitch . external-ids:ovn-bridge-mappings=external-network-provider:ovn0

ovs-vsctl set Interface ovn0  external_ids:iface-id=master01


sysctl -w net.ipv4.ip_forward=1
# iptables -t nat -A POSTROUTING -s 10.1.20.0/24 -o ovn0 -j MASQUERADE

ip a




exit 0

ovs-dpctl show
ovs-dpctl dump-flows
ovs-appctl ovs/route/show
ovs-ofctl show br-int

exit 0



iptables -t nat -L -n --line-number

#iptables -t nat -A POSTROUTING -s 10.10.92.1/24 ! -d 10.10.92.1/24 -j SNAT --to-source 172.17.14.125
#iptables -t nat -A POSTROUTING -s 10.10.92.0/24 -o br-eth0 -j MASQUERADE # eth0连接外网
#iptables   -A FORWARD -i eth0 -o veth-a -j ACCEPT
#iptables   -A FORWARD -i veth-a -o eth0  -j ACCEPT

iptables -t nat -L -n --line-number
# iptables -t nat -D POSTROUTING 21

#iptables -t nat -A POSTROUTING -s 192.168.0.0/24 -o eth0 -j SNAT --to 你的eth0地址
iptables -t nat -A POSTROUTING -s 10.10.92.0/24 -o br-eth0  -j SNAT --to-source 172.17.14.125
iptables -t nat -A POSTROUTING -s 10.10.92.1 -o br-eth0  -j SNAT --to-source 172.17.14.125
iptables -t nat -A POSTROUTING -s 10.10.92.1/24 -o br-eth0 -j MASQUERADE
iptables -A PREROUTING -t nat -j DNAT  -p tcp --dport 8090 -i $if_oam --to 192.168.9.217:8090

ip route add 192.168.10.0/24 via 172.17.14.125 src 10.10.92.1

iptables -t nat -A PREROUTING -d 10.10.92.1  -j DNAT --to-destination 10.10.92.2

route del default gw 192.168.1.1
ip route add 10.10.92.1 via 172.17.14.125



ip route change  default via   172.17.63.253 dev br-eth0

ip route show | column -t

