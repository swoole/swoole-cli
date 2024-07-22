## 分配IPv6 的方式

1. 手动配置：直接设置IPv6地址
2. 基于ICMPv6 NDP 协议的自动配置
3. 基于DHCPv6 协议的自动配置

## IPv6 自动配置IP 的过程中支持三种配置方式

1. SLAAC Stateless address autoconfiguration，无状态地址自动配置
2. Stateful DHCPv6
3. Stateless DHCPv6

## 简述原理

    SLAAC：即无状态自动配置。主机可以通过RA（Router-Advertisement）消息ICMP type134中携带的前缀得到地址的前缀部分，同时通过该接口自动生成接口ID部分，从而得到一个完整的128位的IPV6地址，该消息默认情况下每200S发送一次。当然主机（或路由器）也可以主动发送RS（Router Solicit）消息ICMP Type=133来主动请求该前缀。


    Stateful DHCPv6：即有状态DHCP v6地址分配。通过DHCPv6向主机下发相关前缀、地址、DNS等参数。同时在Stateful DHCPv6又可根据RA报文等配置分为


    Stateless DHCPv6：Stateless 和 Stateful 并不完全冲突，可以同时部署，协同使用。利用 NDP 下发网关和子网前缀等信息，主机根据子网前缀自动生成
    IPv6 地址。同时，利用 DHCPv6 配置 DNS Server 等其他信息。


    IPv6 引入了 ICMPv6 NDP(Neighbor Discovery Protocol,邻居发现协议)来替代 IPv4 ARP(Address Resolution Protocol)

|           | DHCPv6            | NDP                                |
|-----------|-------------------|------------------------------------|
| SLAAC     |                   | 网关、子网前缀、mtu   主机根据子网前缀自动生成 IPv6 地址 |
| Stateful  | 子网前缀、MTU、主机IP、DNS | 子网前缀、网关、主机                         |
| Stateless | DNS               | 子网前缀、网关、主机  、DNS                   |

## 图文详解NDP机制之——地址解析

    NDP使用ICMPv6的5种相关报文

    RS（Router Solicitation）：路由请求报文，type=133，code=0；

    RA（Router Advertisement）：路由通告报文，type=134，code=0；

    NS（Neighbor Solicitation）：邻居请求协议，type=135，code=0；

    NA（Neighbor Advertisement）：邻居通告协议，type=136，code=0；

    重定向：type=137，code=0

