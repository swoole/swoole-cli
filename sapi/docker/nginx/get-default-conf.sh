#!/bin/bash

set -eux
__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}

mkdir -p default-conf

cd default-conf


container_id=$(docker create nginx:1.25-alpine)  # returns container ID
docker cp $container_id:/etc/nginx/ .
docker rm $container_id
