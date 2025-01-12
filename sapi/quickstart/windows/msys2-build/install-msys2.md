## windows 下构建辅助工具 msys2

## 准备 msys2

> windows 系统上快速使用 linux 环境下的命令，msys2 作为辅助工具包，比如准备代理工具

> 打开站点 https://www.msys2.org/ 下载 msys2 安装包

> 使用镜像站点 https://mirror.tuna.tsinghua.edu.cn/help/msys2/  下载 msys2 安装包

> https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/msys2-x86_64-20230526.exe (支持 win7 的最后一版本)

> https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/msys2-x86_64-20240507.exe

> 安装 msys2

> 开始菜单，打开 MSYS2 MSYS 控制台，并安装必要的包,命令如下

> MSYS2 包搜索 https://packages.msys2.org/queue

> [ msys2 各版本环境 区别 ](https://www.msys2.org/docs/environments/)

> msys2 集成了 Mingw 和 Cygwin ，同时还提供了包管理工具 `pacman`

## msys2 安装后初始化

```shell
# 换源 （ 不换源，不需要执行此条命令）
sed -i "s#https\?://mirror.msys2.org/#https://mirrors.tuna.tsinghua.edu.cn/msys2/#g" /etc/pacman.d/mirrorlist*

# 更新源
pacman -Sy --noconfirm
# 无须确认安装包
pacman -Sy --noconfirm git

# msys2 环境下 拉取 swoole-cli 源码
git clone --recursive https://github.com/swoole/swoole-cli.git


```
