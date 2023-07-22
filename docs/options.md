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
./prepare.php --without-docker --with-skip-download=1
```

也可以写作：

```shell
SWOOLE_CLI_SKIP_DOWNLOAD=yes ./prepare.php --without-docker
```

> 参数设置优先于环境变量，当同时使用相同名称的参数设置和环境变量时，
> 环境变量将被忽略，仅参数设置生效，<br/>
> 例如：
> `SWOOLE_CLI_WITH_SKIP_DOWNLOAD=yes ./prepare.php --with-skip-download=no`，
> <br/>
> 有效的值为：
> `--with-skip-download=no`，环境变量 `SWOOLE_CLI_WITH_SKIP_DOWNLOAD=yes` 无效

with-skip-download
----
跳过下载依赖库

> 自动生成待下载链接地址的种子文件<br/>
> 种子文件位于本项目的 `var` 目录 <br/>
> 使用 aria2 下载种子文件

```shell
./prepare.php --with-skip-download=yes --without-docker

# 构建依赖库之前，批量下载依赖库和扩展的脚本
sh sapi/scripts/download-dependencies-use-aria2.sh
sh sapi/scripts/download-dependencies-use-git.sh

```

[使用镜像地址下载](/sapi/download-box/README.md)
----

> 使用镜像地址下载下载前，需要准备镜像服务器
> 例如： `sh sapi/scripts/download-box/download-box-server-run.sh`

```shell
# 演示例子
./prepare.php --without-docker --with-download-mirror-url=http://127.0.0.1:8000

# 可用镜像
./prepare.php --without-docker --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/
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
./prepare.php --with-global-prefix=/usr/local/swoole-cli/
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
sh sapi/scripts/generate-dependency-graph.sh

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

with-install-library-cached
----
使用库缓存，复用已构建、安装的库<br/>
例子：将构建好的openssl库，打包进入容器，使用容器环境构建时，即可跳过 openssl
构建、安装过程

```shell
php ./prepare.php --with-install-library-cached=1
```

with-build-type
----
构建过程 指定构建类型<br/>

debug 调试版本 （构建过程显示，正在执行的构建命令）<br/>
dev 开发版本 （便于调试单个扩展）<br/>
release 默认版本<br/>

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
