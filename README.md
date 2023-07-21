# swoole-cli

`Swoole-Cli` 是一个 `PHP`的二进制发行版，<br/>
集成了 `swoole`、`php 内核`、`php-cli`、`php-fpm`以及多个常用扩展。<br/>
`Swoole-Cli`是全部静态编译打包的，不依赖任何操作系统的`so`
动态链接库，具备非常好的移植性，<br/>
可以在任意 `Linux`/`macOS`/`Windows(CygWin)`系统之间复制，下载即可使用。

> 作为 PHP 开发者都应该知道 PHP 有两种运行模式：php-fpm和php-cli，<br/>
> 那么在 Swoole 5.0 中将迎来一种新的运行模式：swoole-cli。<br/>
> <strong>
> Swoole将像node.js这样作为独立程序提供给用户，而不是作为PHP的一个扩展。
> </strong><br/>
> 除此之外swoole-cli会尽可能地对php-src进行裁剪，<br/>
> 移除一些不用的机制、模块、扩展、函数、类型、常量、代码，
> 使得整个程序可以在几分钟之内编译完成。<br/>
> swoole-cli 是在 PHP 源码的基础上对功能做了裁剪、优化、添加新功能

## 相关文章

- [Swoole-Cli 介绍](https://zhuanlan.zhihu.com/p/581695339)
- [Swoole-Cli 使用说明](https://wenda.swoole.com/detail/108876)
- [Swoole-Cli v5.0.0 版本新特性预览之新的运行模式](https://zhuanlan.zhihu.com/p/459983471)
- [Swoole-Cli 5.0.1 使用说明](https://wenda.swoole.com/detail/108876)
- [Swoole-Cli v5.0.1 PHP 的二进制发行版](https://zhuanlan.zhihu.com/p/581695339)
- [Swoole-Cli v5.0.2 增加 opcache/readline 扩展，强化 Cli-Server](https://zhuanlan.zhihu.com/p/610014616)
- [Swoole-Cli 已提供 Windows 平台 （cygwin64）支持](https://wenda.swoole.com/detail/108743)
- [Swoole 5.1 增加更多数据库协程客户端支持](https://wenda.swoole.com/detail/109023)

## 下载`swoole-cli`发行版

- [https://www.swoole.com/download](https://www.swoole.com/download) (recommend)
- [https://github.com/swoole/swoole-src/releases](https://github.com/swoole/swoole-src/releases)
- [https://github.com/swoole/swoole-cli/releases](https://github.com/swoole/swoole-cli/releases)

## `swoole-cli`构建文档

- [linux 版构建文档](docs/linux.md)
- [macOS 版构建文档](docs/macOS.md)
- [windows Cygwin 版构建文档](docs/Cygwin.md)
- [windows WSL 版构建文档](docs/wsl.md)
- [swoole-cli 构建选项文档](docs/options.md)
- [打包成二进制可执行文件 文档](sapi/samples/sfx/README.md)
- [swoole-cli 搭建依赖库镜像服务](sapi/download-box/README.md)
- [quickstart](sapi/quickstart/README.md)
- [构建 原生 `php-cli` 版本 使用build_native_php分支](https://github.com/swoole/swoole-cli/tree/build_native_php)

## build_native_php 分支与主分支的区别

> 继承 experiment 分支的功能

> 构建原生 PHP 版本

> 可指定 C 编译器 为GCC

> 可编译包含 swow扩展的 PHP二进制可执行文件

> 可禁用编译缓存

## Clone

```shell
git clone --recursive -b build_native_php  https://github.com/swoole/swoole-cli.git
```

## 快速准备 PHP 运行时

```shell
cd swoole-cli

bash setup-php-runtime.sh
# 或者
bash setup-php-runtime.sh --mirror china

```

## 生成构建脚本

```shell
composer install
php prepare.php
php prepare.php +inotify +mongodb -mysqli
```

* 脚本会自动下载相关的`C/C++`库以及`PECL`扩展
* 可使用`+{ext}`或者`-{ext}`增减扩展

## 进入 Docker Bash

```shell
# 启动 alpine 容器环境
bash sapi/quickstart/linux/run-alpine-3.16-container.sh

# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-alpine.sh

# 准备构建基础软件
sh  sapi/quickstart/linux/alpine-3.16-init.sh

```

> 需要将 `swoole-cli` 的目录映射到容器的 `/work` 目录

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

> 打包成功后会生成 `swoole-cli-{version}-{os}-{arch}.tar.xz`
> 压缩包，包含 `swoole-cli` 可执行文件、`LICENSE` 授权协议文件。

## 授权协议

* `swoole-cli` 使用了多个其他开源项目，请认真阅读自动生成的 `bin/LICENSE`
  文件中版权协议，遵守对应开源项目的 `LICENSE`
* `swoole-cli`
  本身的软件源代码、文档等内容以 `Apache 2.0 LICENSE`+`SWOOLE-CLI LICENSE`
  作为双重授权协议，用户需要同时遵守 `Apache 2.0 LICENSE`和`SWOOLE-CLI LICENSE`
  所规定的条款

## SWOOLE-CLI LICENSE

* 对 `swoole-cli` 代码进行使用、修改、发布的新项目必须含有 `SWOOLE-CLI LICENSE`
  的全部内容
* 使用 `swoole-cli`
  代码重新发布为新项目或者产品时，项目或产品名称不得包含 `swoole` 单词
