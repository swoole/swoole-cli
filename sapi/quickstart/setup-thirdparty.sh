#!/bin/bash

set -x
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

# /dev/shm 扩容

# 10G
# mount -o size=10240M -o nr_inodes=1000000 -o noatime,nodiratime -o remount /dev/shm

# du -h -d 1 /dev/shm
# df -h  /dev/shm


exit 0
for dir in `ls -d ${__PROJECT__}`
do
    for file in `ls -R $dir`
    do
        echo $file
    done
done


