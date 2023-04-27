# 快速启动容器环境

> 提供了 debian 11 构建 和 alpine 构建环境
> 任意选一个就可以

## debian 11 构建环境

```bash

# 启动 debian 11 容器环境
sh sapi/quickstart/linux/x86_64/run-debian-11-container.sh

# 进入容器
sh sapi/quickstart/linux/x86_64/connection-swoole-cli-debian.sh

# 准备构建基础软件
sh sapi/quickstart/linux/x86_64/debian-11-init.sh

# 准备构建基础软件 使用镜像
sh sapi/quickstart/linux/x86_64/debian-11-init.sh --mirror china
```

## aline 构建环境

```bash

# 启动 alpine 容器环境
sh sapi/quickstart/linux/x86_64/run-alpine-3.16-container.sh

# 进入容器
sh sapi/quickstart/linux/x86_64/connection-swoole-cli-alpine.sh

# 准备构建基础软件
sh sapi/quickstart/linux/x86_64/alpine-3.16-init.sh

# 准备构建基础软件 使用镜像
sh sapi/quickstart/linux/x86_64/alpine-3.16-init.sh --mirror china

```


## 编译器
- 组合一 clang clang++
- 组合二 musl-gcc g++
