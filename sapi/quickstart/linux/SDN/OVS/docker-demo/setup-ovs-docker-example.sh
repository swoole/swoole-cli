#!/usr/bin/env bash

set -x

docker inspect demo -f '{{.Name}}' > /dev/null
if [[ "$?" -eq 0 ]] ; then
  docker stop demo
  docker rm demo
fi

docker run --name demo  -d --network=none  alpine:edge tail -f /dev/null

PORT=$(ovs-vsctl --data=bare --no-heading --columns=name find interface external_ids:container_id=demo)

if [[ -n "$PORT" ]] ; then
  ovs-vsctl del-port br-int $PORT
fi

ovs-docker add-port br-int eth0 demo --ipaddress="10.1.20.7/24" --gateway="10.1.20.1" --macaddress="00:02:00:00:00:07"

PORT=$(ovs-vsctl --data=bare --no-heading --columns=name find interface external_ids:container_id=demo)

ovs-vsctl set interface $PORT external_ids:iface-id=ls01_port07

