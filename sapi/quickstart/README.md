# 快速初始化构建环境

## 准备 PHP 运行时

```bash

# 准备 PHP 运行时
bash sapi/quickstart/setup-php-runtime.sh

# 准备PHP 运行时 使用代理 （需提前准备好代理)
bash sapi/quickstart/setup-php-runtime.sh --proxy http://192.168.3.26:8015

# 准备PHP 运行时 使用镜像 （镜像源 https://www.swoole.com/download）
bash sapi/quickstart/setup-php-runtime.sh --mirror china

# 容器内准备 PHP 运行时
bash sapi/quickstart/setup-php-runtime-in-docker.sh

# 验证
php -v
composer -v

```

## 准备依赖库源码，来自镜像

> 可能部分源码包没有及时更新 ，请提 issues
> 缺失的部分，下一步执行时会自动到原站下载

```bash

bash sapi/download-box/download-box-get-archive-from-server.sh

```

## 准备 swoole 源码

> 拉取 swoole-cli 源码时没有拉取子模块，就需要执行这一步

```bash

git submodule update --init

```

## 准备构建脚本（会自动下载依赖库源码包）

> [ 构建参数选项 ](../../docs/options.md)

```bash

# 准备 php 运行环境
# macos
alias php='php -d curl.cainfo=/etc/ssl/cert.pem -d openssl.cafile=/etc/ssl/cert.pem'
# linux
alias php='php -d curl.cainfo=/etc/ssl/certs/ca-certificates.crt -d openssl.cafile=/etc/ssl/certs/ca-certificates.crt'

export COMPOSER_ALLOW_SUPERUSER=1
# composer 使用阿里云镜像
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

composer update --no-dev  --optimize-autoloader

php prepare.php  +inotify +apcu +ds +xlswriter +ssh2 --with-swoole-pgsql=1

# 使用镜像下载依赖库源码
php prepare.php  +inotify +apcu +ds +xlswriter +ssh2 --with-swoole-pgsql=1 --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/

# 不启用用 mysqli soap
# php prepare.php  +inotify +apcu +ds -mysqli -soap

# macos
# php prepare.php  +inotify +apcu +ds  --without-docker=1

```

## 准备运行环境 (linux/macos/windows)

1. [ linux 快速启动 容器 构建环环境 ](linux/README.md)
1. [ windows cygwin 快速启动 构建环环境 ](windows/README.md)
1. [ macos 快速启动 构建环环境 ](macos/README.md)

## 构建依赖库 、构建swoole 、打包

```bash

# 构建所有依赖库
bash make.sh all-library

bash make.sh config
bash make.sh build
bash make.sh archive

```

## 更多构建参考文档

1. [cygwin](../../docs/Cygwin.md)
1. [linux](../../docs/linux.md)
1. [macos](../../docs/macOS.md)
1. [wsl](../../docs/wsl.md)
2. [构建选项](../../docs/options.md)
