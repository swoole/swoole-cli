# windows 快速准备构建环境 （2种构建方式）

    1. 原生构建
    2. cygwin 环境 构建

## 一、原生构建

### [windows 原生构建步骤](native-build/README.md)

## 二、cygwin 环境 构建

     cygwin 环境 构建 快速开始，双击如下两个脚本，自动下载cygwin 和 cygwin安装依赖库

```shell

# 自动下载 cygwin
sapi\quickstart\windows\download-cygwin.bat
# 自动安装 cygwin
sapi\quickstart\windows\install-cygwin.bat


```

### [windows cygwin 构建步骤](../../../docs/Cygwin.md)

## 三、其它

### windows 软连接例子

```bash

mklink composer composer.phar

```

### cygwin mirror

    https://cygwin.com/mirrors.html

### msys2 mirror and  environment

    https://www.msys2.org/dev/mirrors/
    https://www.msys2.org/docs/environments/
