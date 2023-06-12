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

## [进入构建 PHP 环节](../README.md#构建依赖库-构建swoole-打包)



