
## 常用命令

```bash
ovn-nbctl show
ovn-sbctl show

ovn-sbctl lflow-list

ovn-sbctl list chassis


ovn-nbctl get-connection
ovn-sbctl get-connection

ss -tuxlpn | grep -e '^\s*tcp\s.*\b:664[0-5]\b' -e '^\s*udp\s.*\b:6081\b' -e '^\s*u_str\s.*\bovn\b' | sed -r -e 's/\s+$//'

```

```bash

ovn-nbctl lr-policy-list lr01
ovn-nbctl lr-route-list lr01
ovn-nbctl ls-lb-list ls10
ovn-nbctl list address_set
ovn-nbctl list acl
ovn-sbctl show
ovn-nbctl find logical_router_policy priority=100


ovn-nbctl list logical_router
ovn-nbctl list logical_router_port
ovn-nbctl list logical_switch
ovn-nbctl list logical_switch_port

```

```bash

ovn-nbctl show
ovn-nbctl lr-policy-list ovn-cluster
ovn-nbctl lr-route-list ovn-cluster
ovn-nbctl ls-lb-list ovn-default
ovn-nbctl list address_set
ovn-nbctl list acl

ovn-nbctl list dhcp_options

ovn-nbctl list gateway_chassis
ovn-nbctl find NAT type=snat

ovn-sbctl list chassis
ovn-sbctl list port_binding
ovn-sbctl show

ovn-sbctl --bare --columns name find Chassis hostname=ovn-gateway-test-01

ovs-appctl ovs/route/show

```

```bash

ovn-nbctl list Logical_Switch

ovn-nbctl list Logical_Switch_Port

ovn-nbctl list ACL

ovn-nbctl list Logical_Router

ovn-nbctl list Logical_Router_Port

ovn-nbctl list Logical_Router_Static_Route

ovn-nbctl list NAT

ovn-nbctl list DHCP_Options

ovn-nbctl list Gateway_Chassis



ovn-sbctl list Chassis

ovn-sbctl list Encap

ovn-sbctl lflow-list

ovn-sbctl --ovs lflow-list

ovn-sbctl list Multicast_Group

ovn-sbctl list Datapath_Binding

ovn-sbctl list Port_Binding

ovn-sbctl list MAC_Binding

ovn-sbctl list DHCP_Options

ovn-sbctl list Gateway_Chassis
```
