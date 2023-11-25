## IPv6 Address Auto Configuration

https://l8liliang.github.io/2022/01/05/ipv6-address-autoconfig.html

## 路由

https://en.m.wikipedia.org/wiki/Routing#Delivery_schemes

## OVN

https://www.cnblogs.com/jingjingxyk/category/2201785.html

```bash
 apt install -y socat libssl-dev ca-certificates
```

## IGMP 组播协议


## OVN command
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
