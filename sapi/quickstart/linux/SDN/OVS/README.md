
## ovn-controller 节点开放 6081 端口

## 验证命令

```bash
ip netns list

ip netns exec vm1 ip a

ip netns exec vm1 ping 10.1.20.2
ip netns exec vm1 ping 10.1.20.3


ip netns exec vm1 ip addr show vm1
ip netns exec vm1 ip route show
ip netns exec vm1 ip neighbor
ip netns exec vm1 ip n



```

```bash

ovs-vsctl show
ovs-vsctl list-br
ovs-vsctl list bridge

ovs-dpctl show
ovs-dpctl dump-flows

ovs-ofctl dump-flows br-int





ovs-appctl ofproto/list-tunnels
ovs-appctl ofproto/list-tunnels
ovs-appctl ofproto/trace ovs-dummy
ovs-appctl ovs/route/show
```


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

# 查看 udp 端口是否开启
nmap -sU -p 6081  192.168.3.244

# 测试MTU
ping -M do -s 1472 10.1.20.2




```




## OVS command
```bash

ovs-ofctl dump-flows br0
ovs-appctl ofproto/list-tunnels
ovs-appctl ofproto/trace ovs-dummy

ovs-appctl ovs/route/show

ovs-dpctl show
ovs-dpctl dump-flows

```
