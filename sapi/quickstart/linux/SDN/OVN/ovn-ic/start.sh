#!/bin/bash
set -exu

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts


proc_num=$(ps -ef | grep 'ovsdb-server -vconsole:off' | grep -v grep | wc -l)
test $proc_num -gt 0 && ( echo 'ovn-controller is running '; kill -15 $$ )

#         --db-ic-nb-port=6645 \
#         --db-ic-sb-port=6646 \

ovn-ctl start_ic_ovsdb

ovn-ic-nbctl set-connection ptcp:6645

ovn-ic-sbctl set-connection ptcp:6646

ovn-ic-nbctl get-connection

ovn-ic-sbctl get-connection

ovn-ic-sbctl show

