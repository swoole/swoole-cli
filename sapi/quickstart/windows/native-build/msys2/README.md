# msys2 环境准备

## [安装 msys2 ](install-msys2.md)

## 下载 msys2 环境 软件包 运行时

```bash

bash sapi/quickstart/windows/native-build/msys2/prepare.sh

bash sapi/quickstart/windows/native-build/msys2/download.sh

```

```bash

# msys2 下载安装  git curl wget openssl zip unzip xz  lzip 软件包
bash sapi/quickstart/windows/native-build/msys2/msys2-install-soft.sh

# 下载 visualstudio 2019
bash sapi/quickstart/windows/native-build/msys2/msys2-download-vs-2019.sh

# 调用 CMD 窗口 安装  vc 运行时
bash sapi/quickstart/windows/native-build/msys2/msys2-install-vc-runtime.sh

# 准备 PHP 运行时
bash sapi/quickstart/windows/native-build/msys2/msys2-download-php-runtime.sh

# 提前准备下载依赖库
bash sapi/download-box/download-box-get-archive-from-server.sh


# 准备 依赖库 和 扩展
bash sapi/quickstart/windows/native-build/msys2/msys2-download-source-code.sh

# 准备 PHP 源码 和 PHP SDK
bash sapi/quickstart/windows/native-build/msys2/msys2-download-php-and-php-sdk.sh

# 构建库准备环境依赖
bash sapi/quickstart/windows/native-build/msys2/msys2-download-deps-soft.sh



```

## 实验

```bash

# 下载辅助软件 （7zip notepad )
bash sapi/quickstart/windows/native-build/msys2/msys2-download-helper-soft.sh

# 下载 visualstudio 2022
bash sapi/quickstart/windows/native-build/msys2/msys2-download-vs-2022.sh


```

## 参考文档
1. [windows php release ](https://windows.php.net/downloads/releases/archives/)
