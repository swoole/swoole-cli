# build static php-cli runtime  and php-fpm

构建静态 原生 php-cli 运行时 和 fastcgi 进程管理器 php-fpm

## 说明

`php-cli` 是一个 `PHP`的 运行时 ，默认包含 swoole 扩展

> 本项目 派生于 [swoole-cli](https://github.com/swoole/swoole-cli/)

> 代码与 swoole-cli 项目的 build_native_php 分支的代码 保持一致

> 构建流程 与 swoole-cli 构建流程一致

> 项目继承 `swoole_cli` 项目的 `main` 分支、`experiment` 分支的构建功能

> 未对 PHP 源码 执行 裁剪、优化、添加新功能等操作

> 可指定 PHP 版本 构建原生 PHP 版本

> 可编译包含 swow 扩展

## 下载`php-cli`发行版

- [https://github.com/swoole/build-static-php/releases](https://github.com/swoole/build-static-php/releases)

## 立即使用 php-cli

```shell

curl -fSL https://github.com/swoole/swoole-cli/blob/build_native_php/setup-php-cli-runtime.sh?raw=true | bash

curl -fSL https://github.com/swoole/build-static-php/blob/main/setup-php-cli-runtime.sh?raw=true | bash

# 指定发布版本
curl -fSL https://github.com/swoole/build-static-php/blob/main/setup-php-cli-runtime.sh?raw=true | bash -s -- --version  v5.1.6.0

```

## 构建文档

- [linux 版构建文档](docs/linux.md)
- [macOS 版构建文档](docs/macOS.md)
- [windows Cygwin 版构建文档](docs/Cygwin.md)
- [windows WSL 版构建文档](docs/wsl.md)
- [php-cli 构建选项文档](docs/options.md)
- [php-cli 搭建依赖库镜像服务](sapi/download-box/README.md)
- [quickstart](sapi/quickstart/README.md)

## Clone

```shell

git clone -b main https://github.com/swoole/build-static-php.git

# 或者

git clone --recursive -b build_native_php  https://github.com/swoole/swoole-cli.git

```

## 快速准备 PHP 运行时

```shell
cd swoole-cli

bash setup-php-runtime.sh
# 或者使用镜像
# 来自 https://www.swoole.com/download
bash setup-php-runtime.sh --mirror china

```

## 快速准备运行环境

### linux

如容器已经安装，可跳过执行安装 docker 命令

```bash

sh sapi/quickstart/linux/install-docker.sh
sh sapi/quickstart/linux/run-alpine-container.sh
sh sapi/quickstart/linux/connection-swoole-cli-alpine.sh
sh sapi/quickstart/linux/alpine-init.sh

# 使用镜像源安装
sh sapi/quickstart/linux/install-docker.sh --mirror china
sh sapi/quickstart/linux/alpine-init.sh --mirror china

```

### macos

如 homebrew 已安装，可跳过执行安装 homebrew 命令

```bash

bash sapi/quickstart/macos/install-homebrew.sh
bash sapi/quickstart/macos/macos-init.sh

# 使用镜像源安装
bash sapi/quickstart/macos/install-homebrew.sh --mirror china
bash sapi/quickstart/macos/macos-init.sh --mirror china

```

## 一条命令执行整个构建流程

```bash

cp build-release-example.sh build-release.sh

# 按你的需求修改配置  OPTIONS="${OPTIONS} --with-libavif=1 "
vi build-release.sh

# 执行构建流程
bash build-release.sh


```

## 生成构建脚本

```shell

composer update
php prepare.php

# 指定PHP 版本
php prepare.php +inotify +mongodb -mysqli --with-php-version=8.2.13

# 使用镜像站下载依赖库
php prepare.php +inotify +mongodb -mysqli --with-download-mirror-url=https://php-cli.jingjingxyk.com/

# 使用代理下载依赖库
php prepare.php +inotify +mongodb -mysqli --with-http-proxy=socks5h://192.168.3.26:2000

# 只编译单个扩展（swoole)
php prepare.php +swoole --with-override-default-enabled-ext=1

# 编译最新版 swoole
php prepare.php -swoole +swoole_latest

# 编译最新版 swow
php prepare.php -swoole +swow_latest

```

* 脚本会自动下载相关的`C/C++`库以及`PECL`扩展
* 可使用`+{ext}`或者`-{ext}`增减扩展

## 构建库之前安装 库依赖 构建环境

```shell

bash make-install-deps.sh

```

## 构建 `C/C++` 依赖库

```shell
./make.sh all-library
```

## 编译配置

```shell
./make.sh config
```

## 构建 php-cli

```shell
./make.sh build
```

> 编译成功后会生成`bin/php-{version}/bin/php`

## 打包

```shell
./make.sh archive
```

> 打包成功后会生成 `php-cli-{version}-{os}-{arch}.tar.xz`
> 压缩包，包含 `php` 可执行文件、`LICENSE` 授权协议文件。

## 授权协议

* `php-cli` 使用了多个其他开源项目，请认真阅读自动生成的 `bin/LICENSE`
  文件中版权协议，遵守对应开源项目的 `LICENSE`
* `php-cli`
  本身的软件源代码、文档等内容以 `Apache 2.0 LICENSE`+`SWOOLE-CLI LICENSE`
  作为双重授权协议，用户需要同时遵守 `Apache 2.0 LICENSE`和`SWOOLE-CLI LICENSE`
  所规定的条款

## SWOOLE-CLI LICENSE

* 对 `swoole-cli` 代码进行使用、修改、发布的新项目必须含有 `SWOOLE-CLI LICENSE`
  的全部内容
* 使用 `swoole-cli`
  代码重新发布为新项目或者产品时，项目或产品名称不得包含 `swoole` 单词
