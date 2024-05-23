# windows 快速准备构建环境 （2种构建方式）

    1. 原生构建
    2. cygwin 环境 构建

## 原生构建

### [windows 原生构建步骤](../../../docs/Cygwin.md)

    1. [install msys2 ](native-build/msys2/install-msys2.md)
    1. [windows build native php](native-build/windows-native.md)

### windows 软连接例子

```bash

mklink composer composer.phar

```
## cygwin 环境 构建

### [windows cygwin 环境 构建步骤](../../../docs/Cygwin.md)

### 双击如下两个脚本，自动下载cygwin 和 cygwin安装依赖库

```shell

# 自动下载 cygwin
sapi/quickstart/windows/cygwin-build/download-cygwin.bat
# 自动安装 cygwin
sapi/quickstart/windows/cygwin-build/install-cygwin.bat


```









