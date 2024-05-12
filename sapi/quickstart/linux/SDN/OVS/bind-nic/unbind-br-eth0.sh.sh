#!/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl set Open_vSwitch .  external-ids:ovn-bridge-mappings=' '

{
    ip addr add  172.16.124.60/20 dev eth0

    ip route replace  default via 172.16.127.253  dev eth0

    ip route add  100.100.2.136 via  172.16.127.253 dev eth0
    ip route add  100.100.2.138 via  172.16.127.253 dev eth0
    ip route add  0.0.0.0       via  172.16.127.253 dev eth0

} || {
  echo $?
}


ovs-vsctl --if-exists del-port  eth0

ip addr flush dev br-eth0

ovs-vsctl --if-exists del-br br-eth0

ip a


