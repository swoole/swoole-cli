# 快速准备基于容器的构建环境

## 从零开始 ( linux debian 环境)

```bash

apt update -y
apt install -y  git socat libssl-dev  zip unzip curl wget ca-certificates  locales
locale-gen en_US.UTF-8

git clone  https://github.com/jingjingxyk/swoole-cli.git

cd swoole-cli

sh sapi/quickstart/linux/install-docker.sh --mirror china

```

## linux 快速初始化容器运行环境

```bash

bash sapi/quickstart/linux/install-docker.sh --mirror china

```

## 运行 alpine 构建环境

```bash

# 启动 alpine 容器环境
bash sapi/quickstart/linux/run-alpine-container.sh
# 使用已经构建好的依赖库 容器环境
# bash sapi/quickstart/linux/run-alpine-container-full.sh

# 进入容器
bash sapi/quickstart/linux/connection-swoole-cli-alpine.sh

# 准备构建基础软件 bash git
sh sapi/quickstart/linux/alpine-init-minimal.sh

# 准备构建基础软件
bash  sapi/quickstart/linux/alpine-init.sh

# 准备构建基础软件 使用中科大镜像源
bash   sapi/quickstart/linux/alpine-init.sh --mirror china

```

## 准备依赖库源码方式一： 来自镜像站

> 默认 github release

```bash

bash sapi/download-box/download-box-get-archive-from-server.sh

bash sapi/download-box/download-box-get-archive-from-server.sh --mirror china

```

## 准备依赖库源码方式二： 来自容器镜像

```bash

bash sapi/download-box/download-box-get-archive-from-container.sh

```

## 准备构建脚本 构建依赖库 、构建swoole 、打包

```bash

 cp build-release-example.sh build-release.sh

 sh build-release.sh
# 使用系统镜像源
# sh build-release.sh --mirror china

```

## 运行 debian 构建环境

> 验证 debian 环境下静态编译

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



