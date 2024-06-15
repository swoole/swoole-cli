#!/usr/bin/env bash


__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

GIT_BRANCH=$(git branch | grep '* ' | awk '{print $2}')
echo "git branch : "$GIT_BRANCH

if [ $GIT_BRANCH = 'new_dev' ] ;then
  echo ' Deleting  folder is not allow in this branch : ' $GIT_BRANCH ;
  exit 0
fi


cd ${__DIR__}/linux/

test -d ceph && rm -rf ceph
test -d kubernetes && rm -rf kubernetes
test -d qemu && rm -rf qemu
test -d SDN && rm -rf SDN


cd ${__PROJECT__}/sapi/src/builder/

test -d library_shared && rm -rf library_shared

cd ${__PROJECT__}/sapi/src/

test -d library_builder && rm -rf library_builder

cd ${__PROJECT__}/sapi/docker/

test -d database && rm -rf database

test -d database-ui && rm -rf database-ui

test -d elasticsearch-neo4j && rm -rf elasticsearch-neo4j

test -d nginx && rm -rf nginx

test -d postgis && rm -rf postgis


cd ${__PROJECT__}
