工具
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
首先需要安装上述工具和库，然后 Clone 项目，并切换 `ext/swoole` 到对应的分支，如 `4.8.x` 或 `master` (`5.0.x`)

```shell
git clone --recursive git@github.com:swoole/swoole-cli.git
```

- 构建：`./sapi/cygwin-build.sh`
- 打包：`bin/swoole-cli sapi/cygwin-pack.php`

打包完成后会在当前目录下生成 `swoole-cli-{version}-cygwin-x64.zip` 压缩包。

备注
----
1. Cygwin 下不支持 `mongodb` 扩展，参考：[https://github.com/mongodb/mongo-php-driver/issues/1381](https://github.com/mongodb/mongo-php-driver/issues/1381)

2. 编译pgsql扩展，在`./sapi/cygwin-build.sh`脚本 `./configure` 后面增加一行： `--with-pgsql --with-pdo-pgsql \`，并将相同版本（如8.1.12）php-src中`ext`目录下的`pgsql` `pdo_pgsql`两个文件夹拷贝到当前项目的ext目录下，再执行构建脚本
