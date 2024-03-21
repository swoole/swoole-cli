# 创建依赖库镜像 和 使用依赖库的镜像

## 系统准备

> 系统 需要 安装 `aria2 ` 用于批量下载

> 系统 需要 安装 `graphviz` 用于生成扩展依赖图

```bash
# macos
brew install graphviz aria2

# alpine
apk add graphviz aria2

```

## 创建依赖库容器镜像

```bash

php prepare.php --skip-download=1 --with-dependency-graph=1 --with-swoole-pgsql=1 +apcu +ds +xlswriter +ssh2 +inotify


# 准备依赖库源码包，使用 aria2 批量下载
bash sapi/download-box/download-box-batch-downloader.sh
# bash sapi/download-box/download-box-batch-downloader.sh --proxy http://192.168.3.26:8015

# 准备 源码包 、扩展依赖图
bash sapi/download-box/download-box-init.sh

# 将源码包 、扩展依赖图 打包到容器中
bash sapi/download-box/download-box-build.sh

```

## 验证打包好的容器

> 本地浏览器打开地址：   [`http://0.0.0.0:9503`](http://0.0.0.0:9503)  即可查看镜像服务器

```bash

bash sapi/download-box/download-box-server-run-test.sh

```

## 依赖库镜像的分发方式

1. 通过容器仓库分发
1. 通过 web 分发
1. 通过 web 分发 （所有源码包打包为一个压缩包文件）

## 依赖库镜像的使用

### 方式一（来自容器分发）：

> 原理：  `docker cp [container_id]:dir dest_dir`

```bash

bash sapi/download-box/download-box-get-archive-from-container.sh

```

### 方式二（来自web服务器）：

> 原理： 下载：`http://127.0.0.1:9503/all-deps.zip`
> 自动解压，并自动拷贝到 `pool/` 目录

>
真实可用的依赖库镜像地址：  `https://swoole-cli.jingjingxyk.com/all-deps.zip`

```bash
bash  sapi/download-box/download-box-get-archive-from-server.sh
```

### 方式三（来自web服务器）：

> 指定镜像地址 单个下载逐步

```bash

# 演示例子
php prepare.php --without-docker=1 --with-download-mirror-url=http://127.0.0.1:9503

# 真实可用的依赖库镜像
php prepare.php --without-docker=1 --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/


```

### 自建镜像站点： 3 种方式：

> 1. `bash sapi/download-box/web-server-nginx.sh`  (直接把 `pool` 作为web根目录)

> 2. `php sapi/download-box/web-server.php`       (直接把 `pool` 作为web根目录)

> 3. 运行包含 `lib` `ext` 目录的容器 , 如下：

> > ` IMAGE=docker.io/jingjingxyk/build-swoole-cli:download-box-nginx-alpine-1.8-20231110T092201Z `

> > ` docker run -d --rm --name download-box-web-server -p 9503:80 ${IMAGE} `
