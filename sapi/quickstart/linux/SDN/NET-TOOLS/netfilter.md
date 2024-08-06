# iptables详解（1）：iptables概念

    https://www.cnblogs.com/jingjingxyk/p/16866170.html

    https://www.cnblogs.com/cheyunhua/p/15188835.html
    https://www.zsythink.net/archives/1199

    firewalld 、nftables 和、iptables 都是 netfilter 管理工具

    四表五链  （链相当于关卡、表相当于系统功能的规则集合）

    放行（accept）、拒绝（reject）和丢弃（drop）

    关卡在iptables中不被称为”关卡”,而被称为”链”

    “路由前”、”转发”、”路由后”，他们的英文名是

    PREROUTING、FORWARD、POSTROUTING

    如果报文需要转发，那么报文则不会经过input链发往用户空间，而是直接在内核空间中经过forward链和postrouting链转发出去

## 常用场景中，报文的流向

    到本机某进程的报文：PREROUTING –> INPUT

    由本机转发的报文：PREROUTING –> FORWARD –> POSTROUTING

    由本机的某进程发出报文（通常为响应报文）：OUTPUT –> POSTROUTING

## iptables为我们提供了如下”表”

我们对每个”链”上都放置了一串规则，但是这些规则有些很相似 ,我们把具有相同功能的规则的集合叫做”表”，所以说，不同功能的规则，我们可以放置在不同的表中进行管理

    filter表：负责过滤功能，防火墙；内核模块：iptables_filter

    nat表：network address translation，网络地址转换功能；内核模块：iptable_nat

    mangle表：拆解报文，做出修改，并重新封装 的功能；iptable_mangle

    raw表：关闭nat表上启用的连接追踪机制；iptable_raw

## 每个”链”中的规则都存在于哪些”表”中

    PREROUTING      的规则可以存在于：raw表，mangle表，nat表。

    INPUT          的规则可以存在于：mangle表，filter表

    FORWARD         的规则可以存在于：mangle表，filter表。

    OUTPUT         的规则可以存在于：raw表mangle表，nat表，filter表。

    POSTROUTING      的规则可以存在于：mangle表，nat表。

## 往往是通过”表”作为操作入口，对规则进行定义的

    raw     表中的规则可以被哪些链使用：PREROUTING，OUTPUT

    mangle  表中的规则可以被哪些链使用：PREROUTING，INPUT，FORWARD，OUTPUT，POSTROUTING

    nat     表中的规则可以被哪些链使用：PREROUTING，OUTPUT，POSTROUTING

    filter  表中的规则可以被哪些链使用：INPUT，FORWARD，OUTPUT

## iptables为我们定义了4张”表”,当他们处于同一条”链”时，执行的优先级如下

    优先级次序（由高而低）：

    raw –> mangle –> nat –> filter

## 链的规则存放于哪些表中（从链到表的对应关系）：

    PREROUTING   的规则可以存在于：raw表，mangle表，nat表。

    INPUT        的规则可以存在于：mangle表，filter表，（centos7中还有nat表，centos6中没有）。

    FORWARD      的规则可以存在于：mangle表，filter表。

    OUTPUT       的规则可以存在于：raw表mangle表，nat表，filter表。

    POSTROUTING  的规则可以存在于：mangle表，nat表。

## ”匹配条件”与”动作”组成了规则

    匹配条件分为基本匹配条件与扩展匹配条件

    基本匹配条件: 源地址Source IP，目标地址 Destination IP

## 处理动作

    处理动作在iptables中被称为target

    ACCEPT：允许数据包通过。

    DROP：直接丢弃数据包，不给任何回应信息，这时候客户端会感觉自己的请求泥牛入海了，过了超时时间才会有反应。

    REJECT：拒绝数据包通过，必要时会给数据发送端一个响应的信息，客户端刚请求就会收到拒绝的信息。

    SNAT：源地址转换，解决内网用户用同一个公网地址上网的问题。

    MASQUERADE：是SNAT的一种特殊形式，适用于动态的、临时会变的ip上。 伪装

    DNAT：目标地址转换。

    REDIRECT：在本机做端口映射。

    LOG：在/var/log/messages文件中记录日志信息，然后将数据包传递给下一条规则，也就是说除了记录以外不对数据包做任何其他操作，仍然让下一条规则去匹配。

## nftables 和 iptables 都是 netfilter 管理工具

## 实操

    当没有使用-t选项指定表时，默认为操作filter表

    iptables -t filter -L
    iptables -t nat    -L
    iptables -t mangle -L
    iptables -t raw    -L

    iptables -t filter -L INPUT
    iptables -v -t filter -L DOCKER  (DOCKER 是自定义链）

    iptables -nvL  (n 不执行IP反查域名）
    iptables --line-numbers -nvL  (n 不执行IP反查域名）显示序号
    iptables --line-numbers -nvL INPUT

    iptables -t 表名 -L 链名

## 主机防火墙  和 网络防火墙

    当iptables作为”网络防火墙”时，在配置规则时，往往需要考虑”双向性”

    state可以译为状态 实现”连接追踪”机制
    报文状态可以为NEW、ESTABLISHED、RELATED、INVALID、UNTRACKED

    连接追踪 对应的内核模块 nf_conntrack

    iptables -I INPUT -m state --state RELATED,ESTABLISHED -j ACCEPT

    TCP Flags： SYN,ACK,FIN,RST,URG,PSH

    conntrack -L

## 共享网络

    iptables   -t nat  -A POSTROUTING    -s 172.16.1.0/24    -j  SNAT  MASQUERADE

## 使用 nftables 代替 iptables

    kubenetes 使用 nftables 代替 iptables (iptable ipvs ipset)

## nftables 查看规则

    nft list rulese
