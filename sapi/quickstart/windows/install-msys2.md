## windows 下构建辅助工具 msys2

## 准备 msys2

> windows 系统上快速使用 linux 环境下的命令，msys2 作为辅助工具包，比如准备代理工具

> 打开站点 https://www.msys2.org/ 下载 msys2 安装包

> 使用镜像站点 https://mirror.tuna.tsinghua.edu.cn/help/msys2/  下载 msys2 安装包

> https://mirrors.tuna.tsinghua.edu.cn/msys2/distrib/x86_64/msys2-x86_64-20230526.exe

> 安装 msys2

> 开始菜单，打开 MSYS2 MSYS 控制台，并安装必要的包,命令如下

> MSYS2 包搜索 https://packages.msys2.org/queue

> [ msys2环境信息 ](https://www.msys2.org/docs/environments/)

> msys2 集成了 Mingw 和 Cygwin ，同时还提供了包管理工具 `pacman`

### msys2 终端下

```shell
# 换源 （ 不换源，不需要执行此条命令）
sed -i "s#https\?://mirror.msys2.org/#https://mirrors.tuna.tsinghua.edu.cn/msys2/#g" /etc/pacman.d/mirrorlist*

# 更新源
pacman -Syy --noconfirm
# 无须确认安装包
pacman -Syy --noconfirm git curl wget openssl zip unzip xz gcc gcc-g++  cmake make

pacman -Syy --noconfirm openssl-devel libreadline


# msys2 环境下 拉取 swoole-cli 源码
git clone --recursive https://github.com:swoole/swoole-cli.git

# msys2 环境下下载 cygwin (也可以用浏览器下载) 安装包
wget https://cygwin.com/setup-x86_64.exe

# 将 cygwin 安装包 移动到 window  指定盘符根目 （这里以 C盘为例）
mv setup-x86_64.exe C:/setup-x86_64.exe


```

### windows 自带默认终端

```shell

# windows 开始菜单，打开 新的 windows 自带终端，执行安装 cygwin
cd c:\

# 添加 pgsql
setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  https://mirrors.ustc.edu.cn/cygwin/ --packages libpq5 libpq-deve

```
