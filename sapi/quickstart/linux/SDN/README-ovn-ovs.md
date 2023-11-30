## IPv6 Address Auto Configuration

https://l8liliang.github.io/2022/01/05/ipv6-address-autoconfig.html

## 路由

https://en.m.wikipedia.org/wiki/Routing#Delivery_schemes

## OVN

https://www.cnblogs.com/jingjingxyk/category/2201785.html


## install ovn

    https://github.com/ovn-org/ovn/blob/main/Documentation/intro/install/general.rst

## ovn conf dir

```bash

#/usr/local/etc/openvswitch
#/usr/local/etc/ovn

```



## debian 环境下 安装 OVN

```bash

apt update -y && apt install -y socat libssl-dev ca-certificates

bash install-ovn-ovs.sh --proxy socks5h://192.168.3.26:2000

bash install-ovn-ovs.sh --proxy http://127.0.0.1:8016

bash install-ovn-ovs.sh --proxy http://127.0.0.1:8016  --mirror china

```

## IGMP 组播协议

## OVN command

    OVN-IC 北向数据库端口，默认为 6645
    OVN-IC 南向数据库端口，默认为 6646

    OVN 北向数据库端口，默认为 6641
    OVN 南向数据库端口，默认为 6642

    ovn-controller 节点开放 6081 端口

```bash
ovn-nbctl ls-list

ovn-nbctl lr-policy-list lr1
ovn-nbctl lr-route-list lr1
ovn-nbctl lr-nat-list lr1
ovn-nbctl lr-lb-list lr1


ovn-nbctl list gateway_chassis
ovn-sbctl list chassis
ovn-nbctl find NAT type=snat
ovn-nbctl find Logical_Router name=lr1

CIDR_IPV4_UUID=$(ovn-nbctl --bare --columns=_uuid find dhcp_options cidr="10.1.20.0/24")


ovn-nbctl list dhcp_options | grep _uuid | awk '{print $3}' | xargs -i ovn-nbctl dhcp-options-del {}

ipv4_num=$(ovn-nbctl --bare --columns=_uuid find dhcp_options cidr="10.1.20.0/24" | wc -l )


```

网卡 混杂模式（promiscuous mode）

```text
# https://patchwork.ozlabs.org/project/openvswitch/patch/20180312112344.13768-1-ligs@dtdream.com/

ovs-vsctl --inactivity-probe=30000 set-manager tcp:<CONTROLLER IP>:6640
ovs-vsctl --inactivity-probe=30000 set-controller tcp:<CONTROLLER IP>:6641
vtep-ctl  --inactivity-probe=30000 set-manager tcp:<CONTROLLER IP>:6640
ovn-nbctl --inactivity-probe=30000 set-connection ptcp:6641:0.0.0.0
ovn-sbctl --inactivity-probe=30000 set-connection ptcp:6642:0.0.0.0

ovn-nbctl set NB_GLOBAL . options:northd_probe_interval=180000
ovn-nbctl set connection . inactivity_probe=180000
ovs-vsctl set open . external_ids:ovn-openflow-probe-interval=180
ovs-vsctl set open . external_ids:ovn-remote-probe-interval=180000
ovn-sbctl set connection . inactivity_probe=180000


# https://mail.openvswitch.org/pipermail/ovs-discuss/2020-August/050554.html

```

Geneve：通用网络虚拟化封装草案
    https://datatracker.ietf.org/doc/draft-ietf-nvo3-geneve/16/
