#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts
set -exu

PROC_NUM=$(ps -ef | grep 'ovn-controller unix:/usr/local/var/run/openvswitch/db.sock' | grep -v grep | wc -l)
if test $PROC_NUM -gt 0 ; then
   echo 'ovn-controller is running ';
   exit 0
fi

sh reset.sh

ipv6=$(ip -6 address show  | grep inet6 | awk '{print $2}' | cut -d'/' -f1 | sed -n '2p')
ipv4=$(ip -4 address show  | grep inet | grep -v 127.0.0 | awk '{print $2}' | cut -d'/' -f1 | sed -n '1p')

OVN_CENTRAL_IP="192.168.3.251"

HOSTNAME="ovn-node-1"
EXTERNAL_IP="$ipv4,$ipv6"
LOCAL_IP="$ipv4,$ipv6"
ENCAP_TYPE=geneve

test -f /usr/local/etc/openvswitch/conf.db && rm -rf /usr/local/etc/openvswitch/conf.db
test -f /usr/local/etc/ovn/conf.db         && rm -rf /usr/local/etc/ovn/conf.db

ID_FILE=system-id.conf
test -s $ID_FILE || cat /proc/sys/kernel/random/uuid > $ID_FILE

CHASSIS_NAME=$(cat $ID_FILE)
ovs-ctl start --system-id=${CHASSIS_NAME}


ovs-vsctl set Open_vSwitch . \
external_ids:system-id="${CHASSIS_NAME}" \
external_ids:hostname="${HOSTNAME}" \
external_ids:ovn-encap-ip="${EXTERNAL_IP}" \
external_ids:ovn-set-local-ip="${LOCAL_IP}" \
external_ids:ovn-encap-type="${ENCAP_TYPE}" \
external_ids:ovn-remote="tcp:${OVN_CENTRAL_IP}:6642"

# external_ids:ovn-nb="tcp:$CENTRAL_IP:6641"

# ovs-vsctl set open . external_ids:ovn-remote-probe-interval=<TIME IN MS>
# ovs-vsctl set open . external_ids:ovn-remote-probe-interval=30000

ovn-ctl start_controller

ovs-vsctl --columns external_ids list open_vswitch

sleep 5
ovs-vsctl list-ports  br-int
# ovs-vsctl set int br-int mtu_request=1450

ovs-ctl status

ss -lnup | grep 6081
