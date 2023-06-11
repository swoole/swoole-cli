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

## [进入构建 PHP 环节](../README.md#构建依赖库-构建swoole-打包)



