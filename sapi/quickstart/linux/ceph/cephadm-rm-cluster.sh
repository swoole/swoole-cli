#!/bin/env bash

set -eux
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}

if [ ! "$BASH_VERSION" ] ; then
    echo "Please do not use sh to run this script ($0), just execute it directly" 1>&2
    exit 1
fi
cephadm rm-cluster --force --zap-osds  --fsid  `awk -F "=" '/fsid/ {print $2}' /etc/ceph/ceph.conf` --force

