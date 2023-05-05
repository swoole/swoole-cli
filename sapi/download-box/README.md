# 创建依赖库镜像 和 使用依赖库的镜像

## 创建依赖库容器镜像

```bash
    sh sapi/download-box/download-box-init.sh
    sh sapi/download-box/download-box-build.sh
```

## 部署依赖库容器镜像

```bash
sh sapi/download-box/download-box-server-run.sh
```

> 本地浏览器打开地址：   [`http://0.0.0.0:8000`](http://0.0.0.0:8000)  即可查看镜像服务器

## 依赖库镜像的分发方式

1. 通过容器仓库分发
1. 通过 web 分发

## 依赖库镜像的使用

### 方式一（来自容器分发）：

> 原理：  `docker cp [container_id]:dir dest_dir`

```bash
    sh sapi/download-box/download-box-get-archive-from-container.sh
```

### 方式二（来自web服务器）：

> 原理： 下载：`http://127.0.0.1:8000/all-archive.zip`
> 自动解压，并自动拷贝到 `pool/` 目录

> 真实可用的依赖库镜像地址：  `https://swoole-cli.jingjingxyk.com/all-archive.zip`

```bash
    sh sapi/download-box/download-box-get-archive-from-server.sh
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
