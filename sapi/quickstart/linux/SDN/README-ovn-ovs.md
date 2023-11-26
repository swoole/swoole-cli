## IPv6 Address Auto Configuration

https://l8liliang.github.io/2022/01/05/ipv6-address-autoconfig.html

## 路由

https://en.m.wikipedia.org/wiki/Routing#Delivery_schemes

## OVN

https://www.cnblogs.com/jingjingxyk/category/2201785.html

## debian 下 安装 OVN

```bash

apt update -y && apt install -y socat libssl-dev ca-certificates

bash install-ovn-ovs.sh --proxy socks5h://192.168.3.26:2000

bash install-ovn-ovs.sh --proxy http://127.0.0.1:8016

```

## IGMP 组播协议

## OVN command
    OVN-IC 北向数据库端口，默认为 6645
    OVN-IC 南向数据库端口，默认为 6646

    OVN 北向数据库端口，默认为 6641
    OVN 南向数据库端口，默认为 6642


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
