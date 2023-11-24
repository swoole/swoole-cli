#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts
set -exu

{
	ovs-ctl stop
	ovn-ctl stop_controller
} || {

	echo $?
}


rm -rf /usr/local/etc/openvswitch/conf.db
rm -rf /usr/local/etc/ovn/conf.db
