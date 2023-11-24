#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
export PATH=$PATH:/usr/local/share/openvswitch/scripts
export PATH=$PATH:/usr/local/share/ovn/scripts

set -exu

{
  ovn-ctl stop_northd
} || {
	echo $?
}

# 不想重置配置，这两句命令不要写
rm -rf /usr/local/etc/ovn/ovnnb_db.db
rm -rf /usr/local/etc/ovn/ovnsb_db.db
