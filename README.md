# swoole-cli

> 说明：需要准备二个构建环境

1. 第一个构建环境用于生成构建脚本
1. 第二个构建环境用于静态编译本项目

## 生成构建脚本

```shell
# 初始化子模块，检出 swoole 扩展
git submodule update --init --recursive

php prepare.php
php prepare.php +inotify +mongodb

```

* 脚本会自动下载相关的`C/C++`库以及`PECL`扩展
* 可使用`+{ext}`或者`-{ext}`增减扩展
* 准备生成构建脚本环境[`prepare-swoole-cli-build-dev-1-container`](build-tools-scripts/prepare-swoole-cli-build-dev-1-container.sh)
* 运行生成构建脚本环境[`run-swoole-cli-build-dev-1-container`](build-tools-scripts/run-swoole-cli-build-dev-1-container)
* 进入容器[`connection-download-container.sh`](build-tools-scripts/connection-download-container.sh)
* 生成构建脚本例子[`build-tools-scripts/download-init-depend.sh`](build-tools-scripts/download-init-depend.sh)
* 生成构建脚本例子使用代理[`build-tools-scripts/download-init-depend-use-proxy.sh`](build-tools-scripts/download-init-depend-use-proxy.sh)

## 进入 Docker Bash

```shell
./make.sh docker-bash
```

> 需要将 `swoole-cli` 的目录映射到容器的 `/work` 目录

* 使用容器环境
* 准备第二阶段静态编译依赖库环境[`build-tools-scripts/prepare-swoole-cli-build-dev-2-container.sh`](build-tools-scripts/prepare-swoole-cli-build-dev-2-container.sh)
* 运行第二阶段静态编译依赖库环境[`build-tools-scripts/run-swoole-cli-build-dev-2-container.sh`](build-tools-scripts/download-init-depend-use-proxy.sh)
* 进入容器[`connection-build-container.sh`](build-tools-scripts/connection-build-container.sh)执行下一步


## 准备依赖库

> 静态编译 依赖库

```shell

./make.sh all-library

```

## 编译配置

```shell
./make.sh config
```

## 构建

```shell
./make.sh build
```

> 编译成功后会生成`bin/swoole-cli`

## 打包

```shell
./make.sh archive
```

## 授权协议

* `swoole-cli`使用了多个其他开源项目，请认真阅读 [LICENSE](bin/LICENSE) 文件中版权协议，遵守对应开源项目的`LICENSE`
* `swoole-cli`本身的软件源代码、文档等内容以`Apache 2.0 LICENSE`+`SWOOLE-CLI LICENSE`作为双重授权协议，用户需要同时遵守`Apache 2.0 LICENSE`和`SWOOLE-CLI LICENSE`所规定的条款

## SWOOLE-CLI LICENSE

* 对`swoole-cli`代码进行使用、修改、发布的新项目必须含有`SWOOLE-CLI LICENSE`的全部内容
* 使用`swoole-cli`代码重新发布为新项目或者产品时，项目或产品名称不得包含`swoole`单词
