# 构建依赖库容器镜像

> 目的： 提前构建好依赖库，使用时直接跳过依赖库构建步骤

> 借助容器的多阶段构建功能，提前构建好依赖库
> 工作目录位于 `var` 目录

## 构建依赖库容器镜像的2种方式说明

> 通过 docker commit 生成 比如 `phpswoole/swoole-cli-builder:1.6`

> 通过 Dockerfile 多阶段构建生成

> 比如 `docker.io/jingjingxyk/build-swoole-cli:all-dependencies-alpine`

> 二者容器镜像是一样的



## 执行构建依赖库容器

```bash

bash build-release-example.sh --mirror china  --build-contianer
## composer 使用腾讯镜像源 , 系统源使用 ustc 源
bash sapi/multistage-build-dependencies-container/all-dependencies-build-container.sh --composer_mirror tencent --mirror ustc

```

## 验证构建好的依赖库

```bash

bash sapi/multistage-build-dependencies-container/all-dependencies-run-container.sh

# 新开终端进入容器
docker exec -it swoole-cli-alpine-dev sh


bash build-release-example.sh --mirror china

```

## 为了方便分发，把容器镜像导出为文件

> 构建加速建议： 使用 抢占式 高配置云服务器 来加速构建
> 目的：节省网络传输流量 （单个文件不压缩情况下，大小超过 1GB）

```bash

cd var

docker save -o "all-dependencies-container-image-$(uname -m).tar" $(cat all-dependencies-container.txt)

# xz 并行压缩 -T cpu核数 -k 保持源文件
xz -9 -T$(nproc) -k "all-dependencies-container-image-$(uname -m).tar"

# xz 解压
xz -d -T$(nproc) -k "all-dependencies-container-image-$(uname -m).tar.xz"

# 从文件导入容器镜像

docker load -i  "all-dependencies-container-image-$(uname -m).tar"


```

## 容器多阶段构建镜像参考文档

- [multistage-build](https://docs.docker.com/develop/develop-images/multistage-build/)
- [dockerfile mount type 挂载目录](https://docs.docker.com/engine/reference/builder/#run---mount)
- [dockerfiles-now-support-multiple-build-contexts](https://www.docker.com/blog/dockerfiles-now-support-multiple-build-contexts/)
