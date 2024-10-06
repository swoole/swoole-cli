# 快速初始化构建环境

## 一个脚本执行整个构建流程

> 定制 build-release-php.sh 脚本 即可开始构建

```bash

cp  build-release-example.sh  build-release-php.sh

bash build-release-php.sh

```

## [构建选项](../../docs/options.md)

## [linux 环境下构建 完整步骤](../../docs/linux.md)

## [macos 环境下构建 完整步骤](../../docs/macOS.md)

## [cygwin](../../docs/Cygwin.md)

## [wsl](../../docs/wsl.md)

## 准备运行环境 (linux/macos/windows)

1. [ linux 快速启动 容器 构建环环境 ](linux/README.md)
1. [ windows cygwin 快速启动 构建环环境 ](windows/README.md)
1. [ macos 快速启动 构建环环境 ](macos/README.md)
1. [ 构建选项 ](../../docs/options.md)

## 相同功能命令 不同写法

```shell

git clone --recursive https://github.com/swoole/swoole-cli.git

git submodule update --init --recursive

```
