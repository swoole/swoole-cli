# swoole-cli

## [Swoole-Cli 介绍](https://zhuanlan.zhihu.com/p/581695339)

> Swoole-Cli 是一个 PHP 的二进制发行版，集成了 swoole、php 内核、php-cli、php-fpm以及多个常用扩展。Swoole-Cli 是全部静态编译打包的，不依赖任何操作系统的so动态链接库，具备非常好的移植性，可以在任意 Linux 系统之间复制，下载即可使用。

> 作为 PHP 开发者都应该知道 PHP 有两种运行模式：php-fpm和php-cli，那么在 Swoole 5.0中将迎来一种新的运行模式：swoole-cli。   
> <strong>Swoole 将像node.js这样作为独立程序提供给用户，而不是作为PHP的一个扩展</strong>。   
> 除此之外swoole-cli会尽可能地对php-src进行裁剪，移除一些不用的机制、模块、扩展、函数、类型、常量、代码，使得整个程序可以在几分钟之内编译完成。

## [Swoole-Cli 使用说明](https://wenda.swoole.com/detail/108876)

## [Swoole v5.0 版本新特性预览之新的运行模式](https://zhuanlan.zhihu.com/p/459983471)

## 下载`swoole-cli`发行版

- [swoole-cli release site 1 (recommend)](https://www.swoole.com/download)
- [swoole-cli release site 2](https://github.com/swoole/swoole-src/releases)
- [swoole-cli release site 3](https://github.com/swoole/swoole-cli/releases)


## Clone
```shell
git clone --recursive git@github.com:swoole/swoole-cli.git
```

或者
```shell
git clone git@github.com:swoole/swoole-cli.git
git submodule update --init
```

## 生成构建脚本

```shell
compser update
php prepare.php
php prepare.php +inotify +mongodb
```

* 脚本会自动下载相关的`C/C++`库以及`PECL`扩展
* 可使用`+{ext}`或者`-{ext}`增减扩展

## 进入 Docker Bash

```shell
./make.sh docker-bash
```

> 需要将 `swoole-cli` 的目录映射到容器的 `/work` 目录

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
