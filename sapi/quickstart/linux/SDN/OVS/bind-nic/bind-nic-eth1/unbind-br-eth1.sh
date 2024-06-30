#!/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl set Open_vSwitch .  external-ids:ovn-bridge-mappings=' '

{
    ip addr add  172.16.124.60/20 dev eth1

} || {
  echo $?
}


ovs-vsctl --if-exists del-port  eth1

ip addr flush dev br-eth1

ovs-vsctl --if-exists del-br br-eth1

ip a


