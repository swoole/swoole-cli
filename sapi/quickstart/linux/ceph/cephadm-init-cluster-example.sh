#!/bin/env bash

set -eux
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}

if [ ! "$BASH_VERSION" ] ; then
    echo "Please do not use sh to run this script ($0), just execute it directly" 1>&2
    exit 1
fi


cat <<EOF > initial-ceph.conf
[global]
osd crush chooseleaf type = 0
EOF

cephadm bootstrap \
--no-cleanup-on-failure \
--cluster-network 192.168.3.0/24 \
--mon-ip 192.168.3.206 \
--dashboard-password-noupdate \
--initial-dashboard-user admin \
--initial-dashboard-password ceph \
--allow-fqdn-hostname \
--single-host-defaults \
--config initial-ceph.conf

exit 0


## 单集群
https://www.redhat.com/sysadmin/ceph-cluster-single-machine
