#!/bin/env bash

set -eux

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

ovs-vsctl set Open_vSwitch . external-ids:ovn-bridge-mappings=' '

{
  ip link set ovn0 down
} || {
  echo $?
}

ovs-vsctl --if-exists del-port ovn0
ovs-vsctl --if-exists del-br ovn0

ip a
