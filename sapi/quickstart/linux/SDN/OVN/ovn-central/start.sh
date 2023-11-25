#!/bin/bash
set -exu

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts


ovn-ctl start_northd # center need
ovn-nbctl set-connection ptcp:6641
ovn-sbctl set-connection ptcp:6642
ovn-nbctl show
ovn-sbctl show

sleep 2

netstat -antp | grep 6641
netstat -antp | grep 6642


# netstat -lntp
