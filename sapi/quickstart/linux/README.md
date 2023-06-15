# 快速启动容器环境

> 提供了 debian 11 构建 和 alpine 构建环境

> 任意选一个就可以

## 快速初始化容器运行环境

```bash

curl -fsSL https://get.docker.com -o get-docker.sh

# 方法一：默认
bash get-docker.sh

# 方法二： 使用镜像地址替换 （使用中科大镜像) 在中国大陆 推荐这个
bash sapi/quickstart/linux/install-docker.sh --mirror china

# 方法三： 使用 阿里云镜像
bash get-docker.sh --mirror Aliyun

# 方法三： 使用 AzureChinaCloud 镜像
bash get-docker.sh --mirror AzureChinaCloud

```

## 构建环境

> debian 和 alpine 任意选一个

> 推荐 alpine

## debian 11 构建环境

```bash

# 启动 debian 11 容器环境
bash sapi/quickstart/linux/run-debian-container.sh

# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-debian.sh

# 准备构建基础软件
bash sapi/quickstart/linux/debian-init.sh


# 准备构建基础软件 使用中科大镜像源
bash sapi/quickstart/linux/debian-init.sh --mirror china
```

## aline 构建环境

```bash

# 启动 alpine 容器环境
bash sapi/quickstart/linux/run-alpine-container.sh


# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-alpine.sh

# 准备构建基础软件
sh  sapi/quickstart/linux/alpine-init.sh

# 准备构建基础软件 使用中科大镜像源
sh  sapi/quickstart/linux/alpine-init.sh --mirror china

```

## 体检构建好 所有依赖库的容器

> 跳过依赖库构建

```shell
# 启动 alpine 容器环境 (容器内包含所有依赖库、php运行时、composer )
bash sapi/quickstart/linux/run-alpine-container-full.sh

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

# 不启用 mysqli soap 例子
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

## [进入构建 PHP 环节](../README.md#构建依赖库-构建swoole-打包)




