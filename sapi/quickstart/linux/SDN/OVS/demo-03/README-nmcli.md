
```bash

lsmod | grep openvswitch

systemctl status networking #old
systemctl status NetworkManager #new



```

```bash
nmcli device
nmcli -t device
nmcli -f DEVICE,TYPE device
nmcli -t -f DEVICE,TYPE device
# 列出可用的网络连接
nmcli connection show
nmcli connection show --active

nmcli device status

nmcli -a
nmcli c help

nmcli general logging

nmcli connection show

nmcli -c no

ip a s

```

```bash

nmcli con down "Wired connection 1"

nmcli con up br-eth0


```


```bash

vi /etc/NetworkManager/NetworkManager.conf




ls -lh /etc/NetworkManager/system-connections/

nmcli connection modify <连接名称> ipv4.gateway <网关 IP 地址>
nmcli connection up <连接名称>

# https://developer-old.gnome.org/NetworkManager/stable/nm-openvswitch.html
# https://github.com/NetworkManager/NetworkManager/blob/main/man/nm-openvswitch.xml





nmcli con add type ethernet slave-type ovs-port master port0 interface-name br-eth0

nmcli con add type ovs-interface slave-type ovs-port master port0 interface-name br-int

```

配置例子
https://access.redhat.com/documentation/en-us/red_hat_enterprise_linux/7/html/networking_guide/sec-configuring_ip_networking_with_nmcli
