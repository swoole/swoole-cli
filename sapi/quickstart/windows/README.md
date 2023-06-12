# windows 快速准备构建环境

## 准备 msys2

> 打开站点 https://www.msys2.org/ 下载 msys2 安装包

> 使用镜像站点 https://mirror.tuna.tsinghua.edu.cn/help/msys2/  下载 msys2 安装包

> 安装 msys2

> 开始菜单，打开 MSYS2 MSYS 控制台

### msys2 准备必要依赖

```shell
# 换源
sed -i "s#https\?://mirror.msys2.org/#https://mirrors.tuna.tsinghua.edu.cn/msys2/#g" /etc/pacman.d/mirrorlist*

# 更新源
pacman -Sy --noconfirm
# 无须确认安装包
pacman -Sy --noconfirm git curl wget openssl zip unzip xz

```

## 准备 swoole-cli源码

> 打开 MSYS2 MSYS 控制台 拉取 swoole-cli 源码

```shell

df -h
cd c:

git clone --recursive https://github.com/swoole/swoole-cli.git


# 借助 wget 下载 cygwin.exe
cd swoole-cli
wget https://cygwin.com/setup-x86_64.exe
mv setup-x86_64.exe ..

```

## 准备 cygwin 环境

> 打开站点 https://cygwin.com/

>
下载 [https://cygwin.com/setup-x86_64.exe](https://cygwin.com/setup-x86_64.exe)

> 安装包

> 命令行 进入 `setup-x86_64.exe` 所在目录，执行下列命令初始安装cygwin

```shell
setup-x86_64.exe  --site https://mirrors.ustc.edu.cn/cygwin/

```

## 安装cygwin依赖

> 开始菜单 打开 cmd

> 命令行 进入 `setup-x86_64.exe` 所在目录，执行下列命令安装依赖

```shell

setup-x86_64.exe --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus --site https://mirrors.ustc.edu.cn/cygwin/ --packages make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel,libsqlite3-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel,libMagick-devel,libzstd-devel,libbrotli-devel,libreadline-devel,libintl-devel,libpq-devel,libssh2-devel,libidn2-devel,gettext-devel,coreutils,openssl-devel,zip,unzip

```

## [进入构建 PHP 环节](../../../docs/Cygwin.md#构建)


VC++

https://aka.ms/vs/16/release/VC_redist.x64.exe

windows build
https://wiki.php.net/internals/windows/stepbystepbuild

