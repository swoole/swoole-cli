# cygwin 环境下构建 swoole-cli

## [安装 cygwin 脚本](/sapi/scripts/cygwin/install-cygwin.sh)

> 没有 wget
> 命令，可以先安装 [msys2环境](https://www.msys2.org/docs/environments/)

> 点击打开 windows 开始菜单，打开 MSYS2 MSYS 控制台

> 使用`pacman`包管理工具，安装 git wget

> msys2 如果慢,可以换源：参考 https://mirrors.cernet.edu.cn/list/msys2

```shell

pacman -Sy --noconfirm git curl wget openssl zip unzip xz

# msys2 环境下 拉取 swoole-cli 源码
git clone --recursive https://github.com:swoole/swoole-cli.git

# msys2 环境下下载 cygwin (用浏览器下载就行) 安装包
wget https://cygwin.com/setup-x86_64.exe

# 将 cygwin 安装包 移动到 window  指定盘符根目 （这里以 C盘为例）
mv setup-x86_64.exe C:/setup-x86_64.exe

```

## 安装 cygwin 环境下的工具列表和依赖

> 点击打开 windows 开始菜单,点击打开 CMD 控制台，并安装必要的包

> 打开 https://cygwin.com/

> cygwin 搜索包 https://cygwin.com/cgi-bin2/package-grep.cgi?grep=openssl

> cygwin 可用 换源地址 参考 https://mirrors.cernet.edu.cn/list/cygwin

```bash

cd c:/

# 安装cygwin 和 cygwin 依赖项

setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  http://mirrors.ustc.edu.cn/cygwin/ --packages make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel,libsqlite3-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel,libMagick-devel,libzstd-devel,libbrotli-devel,libreadline-devel,libintl-devel,libpq-devel,libssh2-devel,libidn2-devel,gettext-devel,coreutils,openssl-devel

setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  http://mirrors.ustc.edu.cn/cygwin/ --packages zip unzip


```

## [ 脚本构建步骤 ](/sapi/scripts/cygwin/README.md)

工具列表
----

- make
- autoconf
- automake
- libtool
- bison
- wget
- tar
- gcc-g++
- openssl
- re2c （需要源码安装）
- zip/unzip（用于压缩打包）

库
----

```
libssl-devel
libcurl-devel
libxml2-devel
libxslt-devel
libgmp-devel
ImageMagick
libpng-devel
libjpeg-devel
libfreetype-devel
libwebp-devel
libsqlite3-devel
zlib-devel
libbz2-devel
libzip-devel
libicu-devel
libonig-devel
libcares-devel
libsodium-devel
libyaml-devel
libMagick-devel
libzstd-devel
libbrotli-devel
libreadline-devel
libintl-devel
libpq-devel (如果编译pgsql扩展)
```

构建
------
首先需要安装上述工具和库，然后 Clone 项目，并切换 `ext/swoole`
到对应的分支，如 `4.8.x` 或 `master` (`5.0.x`)

```shell
git clone --recursive https://github.com:swoole/swoole-cli.git
```

- 准备re2c：` bash ./sapi/scripts/cygwin/install-re2c.sh`
- 准备扩展：`  bash ./sapi/scripts/cygwin/cygwin-config-ext.sh`
- 预处理：`  bash ./sapi/scripts/cygwin/cygwin-config.sh`
- 构建：`   bash ./sapi/scripts/cygwin/cygwin-build.sh`
- 打包：`   bash ./sapi/scripts/cygwin/cygwin-archive.sh`

打包完成后会在当前目录下生成 `swoole-cli-{version}-cygwin-x64.zip` 压缩包。

备注
----

1. Cygwin 下不支持 `mongodb`
   扩展，参考：[https://github.com/mongodb/mongo-php-driver/issues/1381](https://github.com/mongodb/mongo-php-driver/issues/1381)

2. 编译pgsql扩展，在`./sapi/scripts/cygwin/cygwin-build.sh`脚本 `./configure`
   后面增加一行： `--with-pgsql --with-pdo-pgsql \`
   ，并将相同版本（如8.1.12）php-src中`ext`目录下的`pgsql` `pdo_pgsql`
   两个文件夹拷贝到当前项目的ext目录下，再执行构建脚本

# 问题

## [为什么不使用 msys2 构建 swoole-cli,因为 msys2 环境缺失一些依赖库](https://github.com/swoole/swoole-cli/issues/108)
