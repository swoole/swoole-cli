#!/bin/bash
set -exu

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__} &&
{
ip link add dev wg0 type wireguard
} || {
 echo $?

}
ip address add 10.192.99.1/24 dev wg0

wg setconf wg0 configuration.conf
ip link set up  dev wg0
{
  iptables -t filter -A FORWARD -i wg0 -j ACCEPT
} || {
  echo $?
}
wg show wg0

