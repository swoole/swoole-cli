#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex


ovn-nbctl list dhcp_options | grep _uuid | awk '{print $3}' | xargs -i ovn-nbctl dhcp-options-del {}


ovn-nbctl --if-exists ls-del ls10
ovn-nbctl ls-add ls10


ipv4_num=$(ovn-nbctl --bare --columns=_uuid find dhcp_options cidr="10.1.20.0/24" | wc -l )

if test $ipv4_num -ne 1
then
{
    test $ipv4_num -gt 1 && ovn-nbctl --bare --columns=_uuid find dhcp_options cidr="10.1.20.0/24" | awk '{print $1}' | xargs -i ovn-nbctl dhcp-options-del {}
    ovn-nbctl dhcp-options-create "10.1.20.0/24"
}
fi
CIDR_IPV4_UUID=$(ovn-nbctl --bare --columns=_uuid find dhcp_options cidr="10.1.20.0/24")

# https://docs.openstack.org/neutron/latest/ovn/dhcp_opts.html
#server_id– 虚拟 dhcp 服务器的 ip 地址
#server_mac– 虚拟 dhcp 服务器的 MAC 地址
#lease_time– DHCP 租约的生命周期
#router键提供有关默认网关的信息

ovn-nbctl dhcp-options-set-options ${CIDR_IPV4_UUID} \
  lease_time=3600 \
  router="10.1.20.1" \
  server_id="10.1.20.1" \
  server_mac=ee:ee:02:00:00:01 \
  mtu=1400 \
  dns_server="223.5.5.5"

ovn-nbctl dhcp-options-get-options ${CIDR_IPV4_UUID}

ovn-nbctl list dhcp_options

ovn-nbctl set logical_switch ls10 \
other_config:subnet="10.1.20.0/24" \
other_config:exclude_ips="10.1.20.244..10.1.20.254"


ovn-nbctl lsp-add ls10 ls10-port2
ovn-nbctl lsp-set-addresses ls10-port2 '00:02:00:00:00:02 10.1.20.2'
ovn-nbctl lsp-set-port-security ls10-port2  '00:02:00:00:00:02 10.1.20.2'
ovn-nbctl lsp-set-dhcpv4-options ls10-port2 $CIDR_IPV4_UUID




#添加第二个 logical port
ovn-nbctl lsp-add ls10 ls10-port3
ovn-nbctl lsp-set-addresses ls10-port3 '00:02:00:00:00:03 10.1.20.3'
ovn-nbctl lsp-set-port-security ls10-port3 '00:02:00:00:00:03 10.1.20.3'
ovn-nbctl lsp-set-dhcpv4-options ls10-port3 $CIDR_IPV4_UUID

#添加第三个 logical port
ovn-nbctl lsp-add ls10 ls10-port4
ovn-nbctl lsp-set-addresses ls10-port4 '00:02:00:00:00:04 10.1.20.4'
ovn-nbctl lsp-set-port-security ls10-port4 '00:02:00:00:00:04 10.1.20.4'
ovn-nbctl lsp-set-dhcpv4-options ls10-port4 $CIDR_IPV4_UUID

ovn-nbctl list logical_switch_port
ovn-nbctl --columns dynamic_addresses list logical_switch_port
ovn-nbctl show



ovn-nbctl --if-exists lr-del lr1
ovn-nbctl lr-add lr1

ovn-nbctl lrp-add lr1 lr1-ls10-port1   ee:ee:01:00:00:01 10.1.20.1/24


ovn-nbctl lsp-add ls10 ls10-lr1-port1
ovn-nbctl lsp-set-type ls10-lr1-port1 router
ovn-nbctl lsp-set-addresses ls10-lr1-port1 router

ovn-nbctl lsp-set-options ls10-lr1-port1 router-port=lr1-ls10-port1





ovn-nbctl lrp-add lr1 lr1-public-port1   ee:ee:01:00:00:02 100.64.0.1/24



ovn-nbctl  --if-exists ls-del  public
ovn-nbctl ls-add public

ovn-nbctl lsp-add public public-lr1-port1
ovn-nbctl lsp-set-type public-lr1-port1 router
ovn-nbctl lsp-set-addresses public-lr1-port1 router
ovn-nbctl lsp-set-options public-lr1-port1 router-port=lr1-public-port1


ovn-nbctl lsp-add public public-port2
ovn-nbctl lsp-set-addresses public-port2     '00:03:00:00:00:02 100.64.0.2'
ovn-nbctl lsp-set-port-security public-port2 '00:03:00:00:00:02 100.64.0.2'

ovn-nbctl lsp-add public public-port3
ovn-nbctl lsp-set-addresses public-port3     '00:03:00:00:00:03 100.64.0.3'
ovn-nbctl lsp-set-port-security public-port3 '00:03:00:00:00:03 100.64.0.3'



ovn-nbctl --policy=dst-ip lr-route-add lr1 "0.0.0.0/0" 100.64.0.1

ovn-nbctl lr-policy-add lr1 32767 "ip4.dst == 10.1.20.0/24"   allow
ovn-nbctl lr-policy-add lr1 32767 "ip4.dst == 100.64.0.0/16"  allow

ovn-nbctl lr-policy-add lr1 30000 "ip4.dst == 192.168.3.250" reroute 100.64.0.3
ovn-nbctl lr-policy-add lr1 30000 "ip4.dst == 192.168.3.249" reroute 100.64.0.2

ovn-nbctl lr-policy-add lr1 29990 "ip4.src == 10.1.20.0/24"  reroute  100.64.0.3

# lr-policy-add ROUTER PRIORITY MATCH ACTION [NEXTHOP]
# https://www.ovn.org/support/dist-docs/ovn-nbctl.8.txt
# https://www.ovn.org/support/dist-docs/

ovn-nbctl lr-policy-list lr1
ovn-nbctl lr-route-list lr1
ovn-nbctl lr-nat-list lr1
ovn-nbctl lr-lb-list lr1

