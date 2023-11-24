#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts
set -exu

proc_num=$(ps -ef | grep 'ovn-controller unix:/usr/local/var/run/openvswitch/db.sock' | grep -v grep | wc -l)
test $proc_num -gt 0 && ( echo 'ovn-controller is running '; exit 0 )

ipv6=$(ip -6 address show  | grep inet6 | awk '{print $2}' | cut -d'/' -f1 | sed -n '2p')
ipv4=$(ip -4 address show  | grep inet | grep -v 127.0.0 | awk '{print $2}' | cut -d'/' -f1 | sed -n '1p')


CENTRAL_IP=192.168.3.251

EXTERNAL_IP="$ipv4,$ipv6"
LOCAL_IP="$ipv4,$ipv6"
ENCAP_TYPE=geneve
id_file=system-id.conf
test -s $id_file || cat /proc/sys/kernel/random/uuid > $id_file

chassis_name=$(cat $id_file)
ovs-ctl start --system-id=${chassis_name}



ovs-vsctl set Open_vSwitch . \
external_ids:ovn-encap-ip="$EXTERNAL_IP" \
external_ids:local_ip="$LOCAL_IP" \
external_ids:ovn-encap-type="$ENCAP_TYPE" \
external_ids:system-id=${chassis_name} \
external_ids:ovn-remote="tcp:${CENTRAL_IP}:6642" \
external_ids:ovn-nb="tcp:$CENTRAL_IP:6641"

ovn-ctl start_controller

ovs-vsctl --columns external_ids list open_vswitch

ovs-ctl status

