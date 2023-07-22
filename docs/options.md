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
参数设置优先于环境变量，当同时使用相同名称的参数设置和环境变量时，环境变量将被忽略，仅参数设置生效，例如：`SWOOLE_CLI_SKIP_DOWNLOAD=yes ./prepare.php --skip-download=no`
，有效的值为：`--skip-download=no`，环境变量 `SWOOLE_CLI_SKIP_DOWNLOAD=yes` 无效

skip-download
----
跳过下载依赖库

> 会自动生成，待下载链接地址
> 链接地址生成在 项目根目录下的 `var` 目录
> 依赖 aria2

```shell
./prepare.php --skip-download=yes --without-docker

# 构建依赖库之前，批量下载依赖库和扩展的脚本
sh sapi/scripts/download-dependencies-use-aria2.sh

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

with-parallel-jobs
----
构建时最大并发进程数；
默认值是 CPU 逻辑处理器数

```shell
php ./prepare.php --with-parallel-jobs=8
```
