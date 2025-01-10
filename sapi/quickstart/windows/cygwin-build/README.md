# windows 快速准备构建环境

## [windows cygwin 环境 构建步骤](../../../../docs/Cygwin.md)

## 自动下载cygwin 和 cygwin安装依赖库

```shell

# 自动下载 cygwin
.\sapi\quickstart\windows\cygwin-build\download-cygwin.bat
# 自动安装 依赖包
.\sapi\quickstart\windows\cygwin-build\install-cygwin.bat


```

### 使用镜像 安装　cygwin 环境依赖包

```
.\sapi\quickstart\windows\cygwin-build\install-cygwin.bat --mirror china

```

### PowerShell 环境中调用批处理命令

```powershell

cmd /c .\sapi\quickstart\windows\cygwin-build\install-cygwin.bat --mirror china

```

## 进入cygwin 环境

```
C:\cygwin64\bin\mintty.exe -i /Cygwin-Terminal.ico -


# 进入项目所在目录 (USER=Administrator)
cd /cygdrive/c/users/${USER}/swoole-cli

```

### cygwin mirror

    https://cygwin.com/mirrors.html

### 搜索包

    https://cygwin.com/cgi-bin2/package-grep.cgi?grep=openssl

### windows 软连接例子

```bash

mklink composer composer.phar

```

