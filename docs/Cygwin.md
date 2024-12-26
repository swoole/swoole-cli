# cygwin 环境下构建 swoole-cli

## 准备 cygwin 软件包

> 打开 https://cygwin.com/

> 下载 cygwin : `https://cygwin.com/setup-x86_64.exe`

> cygwin 搜索软件包 https://cygwin.com/cgi-bin2/package-grep.cgi?grep=openssl

> cygwin 换源地址 参考 https://mirrors.cernet.edu.cn/list/cygwin

> 命令行同时安装多个包，包名之间使用逗号隔开

## windows 环境下 配置 git 环境

1. 禁止Git在提交和检出时进行换行符的自动转换‌
2. 使用`lf` 作为换行符
3. 区分大小写

```shell

# 下载git
curl.exe -fSLo Git-2.47.1-64-bit.exe https://github.com/git-for-windows/git/releases/download/v2.47.1.windows.1/Git-2.47.1-64-bit.exe

# 命令行静默安装 git
start /wait .\Git-2.47.1-64-bit.exe /VERYSILENT /NORESTART /NOCANCEL /SP- /CLOSEONEXIT=1 /DIR="C:\Program Files\Git"


git config --global core.autocrlf false
git config --global core.eol lf
git config --global core.ignorecase false

```

## 安装cygwin 和 cygwin 依赖项

> 打开windows CMD 终端，进入项目目录 ，执行如下命令

```bash

# 自动安装 cygwin 和  cygwin 依赖项
.\sapi\quickstart\windows\cygwin-build\download-cygwin.bat
.\sapi\quickstart\windows\cygwin-build\install-cygwin.bat


```

构建步骤 - 执行的命令
====
> 运行如下步骤，打开 cygwin64 Terminal， 并进入项目目录，执行如下命令

```shell

# 打开 cygwin64 Terminal

git clone --recursive https://github.com/swoole/swoole-cli.git

cd swoole-cli

# git submodule update --init -f

bash ./sapi/scripts/cygwin/install-re2c.sh
bash ./sapi/scripts/cygwin/cygwin-config-ext.sh
bash ./sapi/scripts/cygwin/cygwin-config.sh
bash ./sapi/scripts/cygwin/cygwin-build.sh
bash ./sapi/scripts/cygwin/cygwin-archive.sh


```

构建步骤说明
----

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
   后面增加一行： `--with-pgsql --with-pdo-pgsql --enable-swoole-pgsql \`
   ，并将相同版本（如8.1.12）php-src中`ext`目录下的`pgsql` `pdo_pgsql`
   两个文件夹拷贝到当前项目的ext目录下，再执行构建脚本

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
libpq5 (如果编译pgsql扩展)
libicu-devel
icu
libzstd-devel

```

## 安装 cygwin 和 安装 cygwin 依赖项  具体执行的命令

> 多个包之间 使用逗号分隔

> 编辑修改此文件即可 `.\sapi\quickstart\windows\cygwin-build\install-cygwin.bat`

```bash
# 安装 cygwin
setup-x86_64.exe    --site  https://mirrors.ustc.edu.cn/cygwin/

# 安装 cygwin 依赖项
setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  https://mirrors.ustc.edu.cn/cygwin/ --packages make,git,curl,wget,tar,libtool,bison,gcc-g++,autoconf,automake,openssl,libpcre2-devel,libssl-devel,libcurl-devel,libxml2-devel,libxslt-devel,libgmp-devel,ImageMagick,libpng-devel,libjpeg-devel,libfreetype-devel,libwebp-devel,libsqlite3-devel,zlib-devel,libbz2-devel,liblz4-devel,liblzma-devel,libzip-devel,libicu-devel,libonig-devel,libcares-devel,libsodium-devel,libyaml-devel,libMagick-devel,libzstd-devel,libbrotli-devel,libreadline-devel,libintl-devel,libpq-devel,libssh2-devel,libidn2-devel,gettext-devel,coreutils

setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  https://mirrors.ustc.edu.cn/cygwin/ --packages zip unzip icu libicu-devel

setup-x86_64.exe  --no-desktop --no-shortcuts --no-startmenu --quiet-mode --disable-buggy-antivirus    --site  https://mirrors.ustc.edu.cn/cygwin/ --packages libpq5 libpq-devel


# 打开 cygwin64 Terminal
```

