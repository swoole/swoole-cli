# 交换机连接外网



```bash
ss -tlnp

ip route show
ip route list

ifconfig

ip route show table main

ip address show

ip -f inet addr show

ip a show eth0

arp -a

ip -s link

lsmod | grep openvswitch


cat /etc/systemd/resolved.con

cat /etc/netplan/50-cloud-init.yaml


systemctl is-active systemd-resolved

systemctl status systemd-resolved
systemctl restart systemd-resolved


systemctl stop systemd-resolved



systemctl status  systemd-networkd


```



## 查看网关配置

```bash

route -v
# or
netstat -rn

ip route show table local

```


```bash

lsmod | grep openvswitch

systemctl status networking #old
systemctl status NetworkManager #new


```


## 手动配置网络
```bash

vi /etc/network/interfaces

auto eth0
iface eth0 inet static
    address 192.168.0.55 #ip地址
    gateway 192.168.0.1  #网关
    netmask 255.255.255.0 #子网掩码
    broadcast             # 广播地址


systemctl restart networking

or

/etc/init.d/networking restart


```


```bash

dhclient -r eth0

```


```bash

iptables -m conntrack --ctstate NEW

```

```bash

ifconfig <interface_name> alias <new_alias_name> up

ifconfig -a

```

```bash

ip route add <DESTINATION> via <GATEWAY>

ip route add <DESTINATION> via <GATEWAY> <dev> INTERFACE

```
