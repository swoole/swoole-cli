# 创建依赖库镜像 和 使用依赖库的镜像

## 依赖库镜像创建

```bash 
    sh sapi/download-box/download-box-init.sh
    sh sapi/download-box/download-box-build.sh
```

## 依赖库镜像验证

```bash 
sh sapi/download-box/download-box-server-run.sh
```

## 依赖库镜像的分发方式

1. 通过容器仓库分发
1. 通过 web 分发

## 依赖库镜像的使用

### 方式一：

```bash
    sh sapi/download-box/download-box-get-archive-from-container.sh
```

### 方式二：

```bash
    sh sapi/download-box/download-box-get-archive-from-server.sh
```