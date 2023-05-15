# 快速初始化构建环境

## 准备PHP 运行时

```bash

# 准备PHP 运行时
bash sapi/quickstart/setup-php-runtime.sh

# 准备PHP 运行时 使用代理 （需提前准备好代理)
bash sapi/quickstart/setup-php-runtime.sh --proxy http://192.168.3.26:8015

# 准备PHP 运行时 使用镜像 （镜像源 https://www.swoole.com/download）
bash sapi/quickstart/setup-php-runtime.sh --mirror china

# 容器中准备运行时
bash sapi/quickstart/setup-php-runtime-in-docker.sh

php -v
compoer -v


```

## [linux 快速启动容器 构建环环境](linux/README.md)

## [linux](../../docs/linux.md)

## [windows cygwin](../../docs/Cygwin.md)

## [macos ](../../docs/macOS.md)


