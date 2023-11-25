## IPv6 Address Auto Configuration

https://l8liliang.github.io/2022/01/05/ipv6-address-autoconfig.html

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

## ovn-controller 节点开放 6081 端口
