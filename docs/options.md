预处理参数
=====

* `+` 开启扩展
* `-` 关闭扩展
* `@` 设置操作系统
* `--` 参数设置

示例：

```shell
./prepare.php --without-docker +mimalloc -mongodb --with-brotli=yes --conf-path="./conf.d" @linux
```

参数设置也可以使用环境变量来代替，格式为 `SWOOLE_CLI_{$option}`
，需要将参数的中横线`-`替换为下划线`_`，例如：

```shell
./prepare.php --without-docker --skip-download=1
```

也可以写作：

```shell
SWOOLE_CLI_SKIP_DOWNLOAD=yes ./prepare.php --without-docker
```

>
参数设置优先于环境变量，当同时使用相同名称的参数设置和环境变量时，环境变量将被忽略，仅参数设置生效，例如：
`SWOOLE_CLI_SKIP_DOWNLOAD=yes ./prepare.php --skip-download=no`
，有效的值为：`--skip-download=no`，环境变量 `SWOOLE_CLI_SKIP_DOWNLOAD=yes` 无效


skip-download
----
跳过下载依赖库，使用脚本单独批量下载


> 会自动生成，待下载链接地址

> 链接地址文件位于 项目根目录下的 `var/download-box/` 目录

> 依赖 aria2

```shell
# 准备批量待下载链接地址
./prepare.php --skip-download=1 --without-docker

# 构建依赖库之前，批量下载依赖库和扩展的脚本
sh sapi/scripts/download-dependencies-use-aria2.sh

```

with-download-mirror-url
----

> [使用镜像地址下载依赖库源码](/sapi/download-box/README.md)

> 使用镜像地址下载前，需要准备镜像服务

> 例如：`sh sapi/scripts/download-box/web-server-nginx.sh`

```shell
# 演示例子
./prepare.php --without-docker --with-download-mirror-url=http://127.0.0.1:9503

#  下载方式一 （逐个下载源码包）
./prepare.php --without-docker --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/


#  下载方式二 （多个源码包整合为一个压缩文件）
sh  sapi/download-box/download-box-get-archive-from-server.sh

#  下载方式三 （使用容器分发）
sh  sapi/download-box/download-box-get-archive-from-container.sh


```

conf-path
----
设置扩展配置文件的目录，默认仅加载 `conf.d` 目录中的扩展，若希望增加更多扩展，可设置此环境变量。
多个目录使用`:`冒号分割。

```shell
./prepare.php --conf-path="/tmp/swoole-cli/conf1:/tmp/swoole-cli/conf2"
```

without-docker
----
直接在宿主机中构建，不使用 docker

> 在 `macOS` 系统无法使用 `docker`，需指定此参数

with-global-prefix
----
设置依赖库安装目录前缀
默认安装目录前缀： `/usr/local/swoole-cli/`

```shell
php ./prepare.php --with-global-prefix=/usr/local/swoole-cli/
```

with-dependency-graph
----
生成扩展依赖图

> 依赖 graphviz

```shell

# macos
brew install graphviz
# debian
apt install -y graphviz
# alpine
apk add graphviz

```

> 生成扩展依赖库 图 步骤

```shell

# 生成扩展依赖图模板
php ./prepare.php --without-docker --with-dependency-graph=1

# 生成扩展依赖图
sh sapi/extension-dependency-graph/generate-dependency-graph.sh

```

with-downloader
----
指定 `wget` 作为下载器 （默认使用`curl` 作为依赖库和扩展的下载器）

```shell
php ./prepare.php --with-downloader=wget
```

with-swoole-pgsql
----
swoole 启用 --enable-swoole-pgsql

```shell
php ./prepare.php --with-swoole-pgsql=1
```

with-php-version
----
切换 PHP 版本

```shell
php ./prepare.php --with-php-version=8.1.18
```

with-parallel-jobs
----
构建时最大并发进程数；<br/>
默认值是 CPU 逻辑处理器数

```shell
php ./prepare.php --with-parallel-jobs=8
```

with-build-type
----
构建过程 指定构建类型<br/>
构建类型，默认是 release
可选项： release debug dev
debug 调试版本 （构建过程显示，正在执行的构建命令）<br/>
dev 开发版本 （便于调试单个扩展）<br/>
release 默认版本<br/>

with-http-proxy
----
使用HTTP代理下载扩展和扩展依赖库<br/>
需要提前准备好代理

```shell
php ./prepare.php --with-http-proxy=http://192.168.3.26:8015
```

with-c-compiler
----
设置编译器
默认编译器 clang

```shell
php ./prepare.php --with-c-compiler=gcc
```

```shell
php ./prepare.php  --with-build-type=dev
```

with-override-default-enabled-ext
----
覆盖默认启用的扩展<br/>
例子：当添加新扩展时，便于调试

```shell
php ./prepare.php +uuid --with-override-default-enabled-ext=1 --with-build-type=dev
```

with-libavif
---
GD 库支持 AVIF 图片

```shell
php ./prepare.php --with-libavif=1
```

show-tarball-hash
----
计算并显示已下载的源码包 HASH 值


with-libavif
----
GD库支持 AVIF图片格式

with-iouring
----
swoole 启用支持 iouring 特性
