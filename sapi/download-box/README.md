# 创建依赖库镜像 和 使用依赖库的镜像

## 系统准备

> 系统 需要 安装 `aria2 ` 用于批量下载

> 系统 需要 安装 `graphviz` 用于生成扩展依赖图

### 系统要求

```bash
# macos
brew install graphviz aria2

# debian
apt install -y graphviz aria2

# alpine
apk add graphviz aria2

```

## 创建依赖库容器镜像

```bash

# 准备依赖库源码包，使用 aria2 批量下载
bash sapi/download-box/download-box-init.sh

# 将源码包 ，扩展依赖图 打包到容器中
bash sapi/download-box/download-box-build.sh

```

## 部署依赖库容器镜像

```bash

bash sapi/download-box/download-box-server-run.sh

```

> 本地浏览器打开地址：   [`http://0.0.0.0:8000`](http://0.0.0.0:8000)  即可查看镜像服务器

## 依赖库镜像的分发方式

1. 通过容器仓库分发
1. 通过 web 分发

## 依赖库镜像的使用

### 方式一（来自容器分发）：

> 原理：  `docker cp [container_id]:dir dest_dir`

```bash

bash sapi/download-box/download-box-get-archive-from-container.sh

```

### 方式二（来自web服务器）：

> 原理： 下载：`http://127.0.0.1:8000/all-archive.zip`
> 自动解压，并自动拷贝到 `pool/` 目录

> 真实可用的依赖库镜像地址：  `https://swoole-cli.jingjingxyk.com/all-archive.zip`

```bash

bash  sapi/download-box/download-box-get-archive-from-server.sh

```

### 方式三（来自web服务器）：

> 指定镜像地址 单个下载逐步

```bash

# 演示例子
./prepare.php --without-docker=1 --with-download-mirror-url=http://127.0.0.1:8000

# 真实可用的依赖库镜像
./prepare.php --without-docker=1 --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/


```

### 完整例子

```bash

php prepare.php \
--with-build-type=dev \
--with-dependency-graph=1 \
+apcu +ds +inotify \
--without-docker=1 \
--with-download-mirror-url=https://swoole-cli.jingjingxyk.com/


```
