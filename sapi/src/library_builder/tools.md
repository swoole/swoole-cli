```shell

pkg-config --libs libpq
pkg-config --cflags libpq
pkg-config libpq --libs-only-L
pkg-config --modversion libpq


pkg-config libpq --libs --cflags

```



Linux man命令后数字含义  https://blog.csdn.net/u012424148/article/details/86227759



--disable-new-dtags表示使用的是rpath，去掉后编译器默认使用runpath


使用 -Wl,–whole-archive -Wl,–start-group 和 -Wl,–end-group -Wl,-no-whole-archive

# 链接顺序问题
# 搜索 静态库的链接顺序
# 参考 https://bbs.huaweicloud.com/blogs/373470

https://eli.thegreenplace.net/2013/07/09/library-order-in-static-linking

https://bbs.huaweicloud.com/blogs/373470

https://ftp.gnu.org/old-gnu/Manuals/ld-2.9.1/html_node/ld_3.html

macos clang 不支持 -Wl,–whole-archive -Wl,–start-group 和 -Wl,–end-group -Wl,-no-whole-archive


gcc 提供了 -Wl,--as-needed 和 -Wl,--no-as-needed 两个选项，这两个选项一个是开启特性，一个是取消该特性。
-Wl,--as-needed 选项指示最终的可执行文件中只包含必要的链接库信息；
-Wl,--no-as-needed 选项指示在命令行中指定加载的所有库都记录到可执行文件头中，并最终由动态加载器去加载

re2c:
https://github.com/skvadrik/re2c/



https://github.com/multiarch/qemu-user-static
https://hub.docker.com/r/multiarch/qemu-user-static/tags?page=1&name=aarch64


ipv4 私有地址块
https://forum.huawei.com/enterprise/zh/thread/580934319407513600


Tmux 是一个终端复用器（terminal multiplexer）
1. tmate 终端共享神器 (fork 于 tmux)  https://github.com/tmate-io/tmate.git
2. tmux

xtermjs
http://xtermjs.org/

Apache Guacamole  pache Guacamole 是一个无客户端远程桌面网关。它支持 VNC、RDP 和 SSH 等标准协议
https://guacamole.apache.org/


wasmer https://github.com/wasmerio/wasmer.git


macos 终端模拟器 iTerm2
```bash

brew install iTerm2

```

## 网络仿真工具平台

    eNSP(Enterprise Network Simulation Platform)是一款由华为提供的、可扩展的、图形化操作的网络仿真工具平台，
        主要对企业网络路由器、交换机进行软件仿真，完美呈现真实设备实景，支持大型网络模拟，让广大用户有机会在没有真实设备的情况下能够模拟演练，学习网络技术。
        ENSP已于2019年12月31日正式停止服务，ENSP软件目前仅对渠道合作伙伴开放。不面向个人用户开放下载

    gns3   https://baike.baidu.com/item/gns3/8995165
           https://www.gns3.com/

## Android 设备 控制工具
    https://github.com/Genymobile/scrcpy
