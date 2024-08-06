```shell

docker inspect network-name
docker network connect network-name container-name


ovs-docker --help

ovs-docker   add-port BRIDGE INTERFACE CONTAINER [--ipaddress="ADDRESS"]
                    [--gateway=GATEWAY] [--macaddress="MACADDRESS"]
                    [--mtu=MTU]

ovs-vsctl list interface


ovs-docker del-port BRIDGE INTERFACE CONTAINER
ovs-docker del-port br-int eth0 demo


ovs-vsctl set port      tap0  tag=9
ovs-vsctl set interface $PORT type=internal

ovs-docker 源码
https://github.com/jingjingxyk/ovs/blob/master/utilities/ovs-docker
```

```shell


docker run --rm --name demo  --network=none -ti --init alpine:edge


ovs-docker add-port br-int eth0 demo --ipaddress="10.1.20.7/24" --gateway="10.1.20.1" --macaddress="00:02:00:00:00:07"


PORT=$(ovs-vsctl --data=bare --no-heading --columns=name find interface external_ids:container_id=demo)

ovs-vsctl set interface $PORT external_ids:iface-id=ls01_port07

ovs-vsctl del-port br-int $PORT


```
