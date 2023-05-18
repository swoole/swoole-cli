# 快速启动容器环境

> 提供了 debian 11 构建 和 alpine 构建环境

> 任意选一个就可以

## 快速初始化容器运行环境

```bash

curl -fsSL https://get.docker.com -o get-docker.sh
bash get-docker.sh

# 使用 阿里云镜像
bash get-docker.sh --mirror Aliyun

```

## 构建环境

> debian 和 alpine 任意选一个

> 推荐 alpine

## debian 11 构建环境

```bash

# 启动 debian 11 容器环境
bash sapi/quickstart/linux/run-debian-11-container.sh

# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-debian.sh

# 准备构建基础软件
bash sapi/quickstart/linux/debian-11-init.sh


# 准备构建基础软件 使用中科大镜像源
bash sapi/quickstart/linux/debian-11-init.sh --mirror china
```

## aline 构建环境

```bash

# 启动 alpine 容器环境
bash sapi/quickstart/linux/run-alpine-3.16-container.sh

# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-alpine.sh

# 准备构建基础软件
sh  sapi/quickstart/linux/alpine-3.16-init.sh



# 准备构建基础软件 使用中科大镜像源
sh  sapi/quickstart/linux/alpine-3.16-init.sh --mirror china


```

## 准备 PHP 运行时

```bash

# 准备PHP 运行时
bash sapi/quickstart/setup-php-runtime.sh

# 准备PHP 运行时 使用代理 （需提前准备好代理)
bash sapi/quickstart/setup-php-runtime.sh --proxy http://192.168.3.26:8015

# 准备PHP 运行时 使用镜像 （镜像源 https://www.swoole.com/download）
bash sapi/quickstart/setup-php-runtime.sh --mirror china


bash sapi/quickstart/setup-php-runtime-in-docker.sh

php -v
compoer -v


```

## 准备依赖库，来自镜像

> 可能部分源码包没有及时更新 ，请提 issues
> 缺失的部分，下一步执行时会自动到原站下载

```bash

bash sapi/download-box/download-box-get-archive-from-server.sh

```


## 准备构建脚本

```bash

# composer 使用阿里云镜像
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 使用代理
# export http_proxy=http://192.168.3.26:8015
# export https_proxy=http://192.168.3.26:8015

composer update --no-dev  --optimize-autoloader

php prepare.php  +inotify +apcu +ds

# 不起用 mysqli soap
# php prepare.php  +inotify +apcu +ds -mysqli -soap

# macos
# php prepare.php  +inotify +apcu +ds  --without-docker=1

```

## 构建依赖库 、构建swoole 、打包

```bash

chmod a+x ./make.sh

bash make.sh all-library

bash make.sh config
bash make.sh build
bash make.sh archive

```


