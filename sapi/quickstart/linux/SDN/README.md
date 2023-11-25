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

## tools

```bash
ip route show
route -n
netstat -nr

iptables -t nat -L  -n --line-number

tcpdump -i any   port 6081 -v

ethtool

tcpdump -i any   port 6081 -v -n
apt install -y conntrack
# 跟踪它看到的所有报文流
conntrack -L
 # 可显示经过源 NAT 的连接跟踪项
conntrack -L -p tcp –src-nat
tcpdump -i any   not host 192.168.10.3 and not host 192.168.3.26 -v -n


```

```bash

tcpdump -i any -nn port 6081
tcpdump -i any -nnn udp  port 6081

tcpdump -i genev_sys_6081 -vvnn icmp

tcpdump -i eth0 -vvvnnexx


tcpdump -i eth0 -nnn udp  port 6081

tcpdump -ni eth0 -e -c 5

nmap -sU -p 6081  192.168.3.244

# 测试MTU
ping -M do -s 1472 10.1.20.2


```

## ovn-controller 节点开放 6081 端口


## OVS command
```bash

ovs-ofctl dump-flows br0
ovs-appctl ofproto/list-tunnels
ovs-appctl ofproto/trace ovs-dummy

ovs-appctl ovs/route/show

ovs-dpctl show
ovs-dpctl dump-flows

```

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
