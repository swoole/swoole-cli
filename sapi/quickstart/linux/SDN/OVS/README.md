## ovn-controller 节点开放 6081 端口

    ovn Geneve使用UDP封包

## 验证命令

````bash

tcpdump -i any -nn port 6081

tcpdump -i any -nnn udp  port 6081

````

## OVS 设置MTU
```bash
ovs-vsctl list-ports  br-int
ovs-vsctl set int br-int mtu_request=1450

# https://docs.openvswitch.org/en/latest/faq/issues/

```


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

## 查看网卡 MTU

```bash

# 确认使用的网卡
ip route list match 0.0.0.0/0 | awk '{print $5 }'

# apt install -y  network-manager

nmcli

nmcli connection show

nmcli -g connection.interface-name c show genev_sys_6081


```

```bash
ovs-vsctl list open_vswitch
ovs-vsctl -- --columns=name,ofport list Interface

ss -tuxlpn | grep -e '^\s*tcp\s.*\b:664[0-5]\b' -e '^\s*udp\s.*\b:6081\b' -e '^\s*u_str\s.*\bovn\b' | sed -r -e 's/\s+$//'

ss -tlpn

ovs-vsctl show
ovs-vsctl list-br
ovs-vsctl list bridge
ovs-appctl dpif/show

ovs-appctl ofproto/list-tunnels

ovs-appctl ofproto/trace ovs-dummy

# userspace datapath端口信息
ovs-appctl dpctl/show
# 查看路由信息
ovs-appctl ovs/route/show

ethtool -i genev_sys_6081
ethtool -k genev_sys_6081

ovs-appctl dpctl/dump-flows
tcpdump -vne -i  genev_sys_6081
tcpdump -vne -i eth0
tcpdump -vne -i eth0 src host  192.168.3.26


ovs-dpctl-top

ovs-dpctl show
ovs-dpctl dump-flows
ovs-ofctl dump-flows br-int

ovs-appctl bridge/dump-flows br-int

ovs-appctl ofproto/trace br-int
```

## tools

```bash
ip route show
route -n
netstat -nr

iptables -t nat -L  -n --line-number


ethtool


apt install -y conntrack
# 跟踪它看到的所有报文流
modinfo nf_conntrack

cat /proc/net/nf_conntrack

conntrack -L
 # 可显示经过源 NAT 的连接跟踪项
conntrack -L -p tcp –src-nat

conntrack -L -p udp –src-nat

conntrack -L -o extended | awk '{sum[$3]++} END {for(i in sum) print i, sum[i]}' && echo "-----------" && cat /proc/net/nf_conntrack | awk '{sum[$3]++} END {for(i in sum) print i, sum[i]}'



cat /proc/net/udp

# TCP
netstat -st
# UDP
netstat -su

netstat -s -u
netstat -s --udp
watch netstat -su



# 查看某个端口被哪个进程占用
lsof -i:6081

# tcp
netstat -tlnp | grep 6081
# udp
netstat -nlnp | grep 6081

netstat -anup

# 测试UDP 服务端端口
# 参考 https://cloud.tencent.com/developer/article/1432599
nc -v -u -l 0.0.0.0 6081
# 客户端
nc -u -v  192.168.1.1 6081







tcpdump -i any -n udp and src host 192.168.1.1
tcpdump -i any -n udp and src host 192.168.1.1 and dst port 12345

tcpdump -i any -n udp and src host  192.168.3.26

tcpdump -i eth1 udp port xxxx -A -nn

```

```bash

tcpdump -i any   port 6081 -v

tcpdump -i any   port 6081 -v -n

netstat -ni
netstat -s
# https://www.cnblogs.com/andy6/p/8094183.html

# 获得抓包文件
tcpdump -i any   port 6081 -v -n  -w ovn-node-chendu-test.cap



tcpdump -i any   not host 192.168.10.3 and not host 192.168.3.26 -v -n

tcpdump -i any -nn port 6081

tcpdump -i any -nnn udp  port 6081

tcpdump -i genev_sys_6081 -vvnn icmp

tcpdump -i eth0 -vvvnnexx


tcpdump -i eth0 -nnn udp  port 6081

tcpdump -ni eth0 -e -c 5

ethtool -S eth0
ethtool eth0 | egrep 'Speed|Duplex'
ethtool -S eth0 | grep crc
ethtool -S eth0 | grep -i error

ethtool --offload eth0 tx off

# 查看丢包
ethtool -S eth0 | grep rx_ | grep -E "errors|drops"
netstat -i
cat /proc/net/dev

ethtool -k genev_sys_6081


# 查看 udp 端口是否开启
nmap -sU -p 6081  192.168.3.244

# 测试MTU
ping -M do -s 1410 10.1.20.2

# 测试MTU案例
ip netns exec vm1 ping -M do -s 1350 10.1.20.2


ip netns exec vm1 python3 -m http.server 8000

ip netns exec vm1 curl -v http://10.1.20.2:8000

tcpdump -i any port 6081 or icmp  -nneev

ovs-appctl ofproto/trace-packet-out br-int

```

## iperf3 测速

```bash

# 服务端：
    iperf3 -s

# 客户端：
    iperf3 -c 服务器ip地址 -R


apt install -y iperf3

ip netns exec vm1 iperf3 -s

# 测试10分钟
ip netns exec vm1 iperf3 -c 10.1.20.2 -R -t 600


```

```bash
ovs-vsctl get interface vm1  statistics

ovs-vsctl get interface vm1  statistics | sed -e 's#[{}]##g' -e 's#, #\n#g' | grep packets= | grep -v '=0$'

ovs-appctl coverage/show


# package trace

ovs-ofctl show br-int

ovs-appctl ofproto/trace br-int in_port=vm1,tcp,nw_src=10.1.20.3,tcp_dst=6081

ovs-appctl ofproto/trace br-int in_port=vm1,icmp,nw_src=10.1.20.3,nw_dst=10.1.20.2,dl_src=00:02:00:00:00:03,dl_dst=00:02:00:00:00:02
ovs-appctl ofproto/trace br-int in_port=vm1,tcp,nw_src=10.1.20.3,nw_dst=10.1.20.2,dl_src=00:02:00:00:00:03,dl_dst=00:02:00:00:00:02

ovs-appctl ofproto/trace br-int in_port=vm1,icmp,nw_src=10.1.20.3,nw_dst=10.1.20.4,dl_src=00:02:00:00:00:03,dl_dst=00:02:00:00:00:04
ovs-appctl ofproto/trace br-int in_port=vm1,icmp,nw_src=10.1.20.4,nw_dst=10.1.20.3,dl_src=00:02:00:00:00:04,dl_dst=00:02:00:00:00:03

ovs-appctl ofproto/trace br-int in_port=vm1,dl_src=00:02:00:00:00:02,dl_dst=00:02:00:00:00:03

ovs-appctl ofproto/trace br-int in_port=vm1,tcp,nw_src=10.1.20.2,nw_dst=10.1.20.3,ct_state=trk




ovs-ofctl -O OpenFlow13 show br-int



```

```bash

ovs-vsctl list open .
ovs-dpctl show -v


ovs-appctl bridge/dump-flows br-int
ovs-ofctl -O OpenFlow13 dump-flows br-int

ovs-ofctl show br-int
ovs-ofctl dump-ports br-int
ovs-vsctl show


dmesg | tail

```

## 查看日志

```bash

tail -f  /usr/local/var/log/ovn/ovn-controller.log

tail -f  /usr/local/var/log/openvswitch/ovs-vswitchd.log
tail -f  /usr/local/var/log/openvswitch/ovsdb-server.log


```

## SDN｜OpenFlow流表简述

    https://baijiahao.baidu.com/s?id=1690694392596006484


Ethernet over UDP
网络标识符（Virtual Network Identifier，VNI）
通用网络虚拟化封装 Generic Network Virtualization Encapsulation，GENEVE


```bash
# 参考 https://www.jianshu.com/p/c308ecc3b191

ovs-appctl ofproto/trace br-int in_port="vm1",icmp,dl_src=00:02:00:00:00:04,dl_dst=00:02:00:00:00:03,nw_src=10.1.20.4,nw_dst=10.1.20.3,nw_ttl=64


ovn-trace --summary ls01 'inport == "ls01_port04" && eth.src == 00:02:00:00:00:04 && eth.dst == 00:02:00:00:00:03 && ip4.src==10.1.20.4 && ip4.dst==10.1.20.3 && ip.ttl==64'


```

OVS mtu
    https://docs.openvswitch.org/en/latest/faq/issues/

自建SNAT策略后网络不通
    https://help.aliyun.com/zh/vpc/network-failure-after-self-built-snat-policy
网络最大传输单元MTU 修改ECS网卡MTU
    https://help.aliyun.com/zh/ecs/user-guide/mtus
专有网络构成
    https://help.aliyun.com/zh/ecs/user-guide/overview-9
什么是VPN网关
    https://help.aliyun.com/zh/vpn/product-overview/what-is-vpn-gateway
MTU配置说明
    https://help.aliyun.com/zh/vpn/user-guide/set-mtu-values


## 网络管理配置工具
1. netplan
1. NetworkManager （nmcli)
1. systemd-networkd (systemd的一部分 )

```bash

systemctl status systemd-networkd
systemctl status systemd-resolved


```
