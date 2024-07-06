#!/bin/bash
set -exu

__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__} &&


wg show wg0
{
 iptables -t filter -D FORWARD -i wg0 -j ACCEPT
} || {
  echo $?
}

ip link set down  dev wg0
ip address del 10.192.99.1/24 dev wg0
ip link del dev wg0 type wireguard
