#!/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl --if-exists del-br br-eth0
ovs-vsctl add-br br-eth0 # 添加网桥
ip link set br-eth0 up # 激活网桥

{
    # 本机IP地址： 172.16.124.59
    # 本机网关：   172.16.127.253
    # 本机掩码：   255.255.240.0
    # 广播地址：    172.16.127.255

    ip addr add  172.16.124.60/20 dev br-eth0
    ip route replace  default via 172.16.127.253  dev br-eth0

    ip route add  100.100.2.136 via  172.16.127.253 dev br-eth0
    ip route add  100.100.2.138 via  172.16.127.253 dev br-eth0
    ip route add  0.0.0.0/0     via  172.16.127.253 dev br-eth0

    # 测试例子
    # ip route del 100.100.2.136
    # ip route del 100.100.2.138
    # ip route del 172.16.112.0/20

    # ip route replace  default via  192.168.0.1 dev br-eth0
    # ip route change  default via  192.168.0.1 dev br-eth0 src 192.168.0.27


    # ip route del 192.168.0.27/24 via 0.0.0.0
    # ip route add 192.168.0.27/24 via 0.0.0.0 dev br-eth0
    # ip route change  default via  192.168.0.1 dev br-eth0

    # ip route add default via 192.168.10.1 dev eth0
    # ip route add default via 192.168.0.1  dev br-eth0

    # ip route add <DESTINATION> via <GATEWAY> <dev> INTERFACE
    # ip route add 0.0.0.0 via 192.168.0.1 dev br-eth0
    # ip route change  default via  192.168.0.1 dev br-eth0

} ||
{
    echo $?
}

ovs-vsctl add-port br-eth0  eth0
ovs-vsctl set Open_vSwitch . external-ids:ovn-bridge-mappings=external-network-provider:br-eth0

ip addr flush dev eth0

sysctl -w net.ipv4.ip_forward=1
# iptables -t nat -A POSTROUTING -s 10.1.20.0/24 -o br-eth0 -j MASQUERADE

ip a


systemctl stop systemd-resolved

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

