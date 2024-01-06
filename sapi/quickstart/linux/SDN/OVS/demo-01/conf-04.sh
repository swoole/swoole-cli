#!/bin/bash
set -x


ovs_running_flag=$(ps -ef | grep 'ovs-vswitchd unix:/usr/local/var/run/openvswitch/db.sock' | grep -v 'grep')

if test -z "$ovs_running_flag"
  then
    echo 'ovs no running' && exit 1
fi


# grep命令精确匹配字符串查找

flag=$(ip netns list | grep "\<vm1\>")
test -z "$flag"  || ip netns del vm1

ip netns add vm1

ovs-vsctl --if-exists del-port br-int vm1
ovs-vsctl --may-exist add-port br-int vm1 -- set interface vm1 type=internal

ip link set vm1 netns vm1

ip netns exec vm1 ip link set vm1 address 00:02:00:00:00:04
ip netns exec vm1 ip addr add 10.1.20.4/24 dev vm1

ip netns exec vm1 ip route add default via 10.1.20.1 dev vm1

# ip netns exec vm1 ip link set dev vm1  mtu 1400
ip netns exec vm1 ip link set vm1 up
ip netns exec vm1 ip link set lo up
ip netns exec vm1 ip a

ovs-vsctl set Interface vm1 external_ids:iface-id=ls01_port04


ip netns exec vm1 ip addr show
ip netns exec vm1 ip route show
ip netns exec vm1 ip neighbor
ip netns exec vm1 ip n


ip netns exec vm1 ip route replace  default via 10.1.20.2
