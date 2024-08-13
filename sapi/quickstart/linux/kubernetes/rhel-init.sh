#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}/sapi/quickstart/linux/

bash rhel-init-minimal.sh "$@"

# ip link
ifconfig -a
cat /sys/class/dmi/id/product_uuid
# nc 127.0.0.1 6443
stat -fc %T /sys/fs/cgroup/
