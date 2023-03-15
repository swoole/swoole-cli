

预处理参数
=====

* `+` 开启扩展
* `-` 关闭扩展
* `@` 设置操作系统
* `--` 参数设置

示例：
```shell
./prepare.php +mimalloc -mongodb --with-brotli=yes --conf-path="./conf.d" @linux
```

参数设置也可以使用环境变量来代替，格式为 `SWOOLE_CLI_{$option}` ，需要将参数的中横线`-`替换为下划线`_`，例如：

```shell
./prepare.php --skip-download
```

也可以写作：
```shell
SWOOLE_CLI_SKIP_DOWNLOAD=yes ./prepare.php
```

skip-download
----
跳过下载依赖库

> 会自动生成，待下载链接地址
> 链接地址生成在 项目根目录下的 `var` 目录

```shell
./prepare.php --skip-download

# 构建依赖库之前，批量下载依赖库和扩展的脚本
sh sapi/download-dependencies-use-aria2.sh

```

with-brotli
----
是否开启`brotli`压缩

conf-path
----
设置扩展配置文件的目录，默认仅加载 `conf.d` 目录中的扩展，若希望增加更多扩展，可设置此环境变量。
多个目录使用`:`冒号分割。

```shell
./prepare.php -conf-path="/tmp/swoole-cli/conf1:/tmp/swoole-cli/conf2"
```

without-docker
----
直接在宿主机中构建，不使用 docker

> 在 `macOS` 系统无法使用 `docker`，需指定此参数
