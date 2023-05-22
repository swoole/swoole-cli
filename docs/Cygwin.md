# cygwin 环境下构建 swoole-cli

## [安装 cygwin 脚本](/sapi/scripts/cygwin/install-cygwin.sh)

> 没有 wget 可以先用 [msys2](https://www.msys2.org/docs/environments/)

```bash

# download cygwin  下载安装并安装 cygwin (用浏览器下载就行)
 wget https://cygwin.com/setup-x86_64.exe

# cygwin 移动到 bin 目录
mv setup-x86_64.exe C:/cygwin64/bin/setup-x86_64.exe

```

## 安装工具列表和依赖

```bash
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
