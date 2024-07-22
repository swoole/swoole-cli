```text



源IP 源端口 目标IP 目标端口

NAT（Network Address Translation） 网络地址转换技术

NAT 0   无NAT，具有公网IP
NAT 1   源IP和源端口都不受限
NAT 2   源IP受限，而源端口不受限
NAT 3   IP、端口都受限
NAT 4   具有端口受限锥型的受限特性，内部地址每一次请求一个特定的外部地址，都会绑定到一个新的端口 ，只能和 NAT0 设备通信

# 两大类
CONE NAT      圆锥型 NAT  （
    Full Cone                      （NAT 1）  全锥型
    Adress Restricted Cone          （NAT 2） 地址限制锥型  先有内网主机向此外网地址ip:port发送过数据包）
    Port   Restricted  Cone         （NAT 3） 端口限制锥型  先有内网主机向此外网地址ip:port发送过数据包）
SYMMETRIC NAT 对称型 NAT              （NAT 4） 对称型

P2P在NAT和防火墙上的穿透 （于对内网主机的保护,NAT仍然有其存在的必要）
https://blog.csdn.net/nivana999/article/details/5311942
https://www.cnblogs.com/colin-vio/p/13323228.html
https://arthurchiao.art/blog/how-nat-traversal-works-zh/
https://tailscale.com/blog/how-nat-traversal-works/

“STUN”、“TURN”、“ICE”、“uPnP”
ICE (Interactive Connectivity Establishment）
服务器中转 （中继服务）


signalling channel   协调作用

headscale
https://github.com/juanfont/headscale.git

tailscale
https://tailscale.com/blog/how-tailscale-works/

tmate 终端共享神器 (fork 于 tmux)
https://github.com/tmate-io/tmate.git

socat NAT 映射
http://linux.51yip.com/search/socat


SSH 命令的三种代理功能（-L/-R/-D）
https://zhuanlan.zhihu.com/p/57630633
ssh -R

zerotier
  planet  相当于 stun
  moon  相当于中继服务器 （trun)
https://github.com/zerotier/ZeroTierOne.git

wireguard
    51820/51820

strongswan-ikev2
    500/500
    4500/4500

    https://docs.strongswan.org/docs/5.9/howtos/ipsecProtocol.html

openvpn

FRP
https://github.com/fatedier/frp.git

todesk

NET-MAP

UPnP (Universal Plug and Play)


VNC

```


Web前端WebRTC 攻略(五) NAT 穿越与 ICE
    https://mp.weixin.qq.com/s/yIC3pNvQp-YrLUD-pjz1iw

【译】 NAT 穿透是如何工作的：技术原理及企业级实践
    https://arthurchiao.art/blog/how-nat-traversal-works-zh/

webrtc 场景一：双方处于同一个内网中

    直接通过内网进行连接
    通过公网后进行连接
    通过中继服务器进行连接

场景二：双方处于不同的两个地方

    直接通过P2P进行连接
    通过中继服务器进行连接

candidate的字段解析

    foundation:用于标志和区分来自同一个stun的不同的候选者,ID标识
    icegroupid:ICE的组ID
    type:协议类型
    priority:优先级
    ip:ip地址
    port:端口
    typ:标识后面字段的属性类型是候选类型
    host:本地接口获取到的candidate（本机候选）
    srflx:NAT网关在公网侧的IP地址，通过STUN或者TURN收集(server reflexive candidate)（内网主机映射的外网地址端口，对称性NAT）
    prflx:可以在ICE的后续阶段中获取到(peer reflexive candidate)（TUN server为客户端分配的中继地址）
    relay:TURN服务器的公网转发地址，通过TURN收集（中继服务器的地址）


    generation:代号，表明当前是第几代的候选
    ufrag: ICE分配的用户名标识
    network-cost : 网卡标识
