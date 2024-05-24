# msys2 环境准备

## [安装 msys2 ](install-msys2.md)

## 下载 msys2 环境 软件包 运行时

```bash

# 下载安装  git curl wget openssl zip unzip xz  lzip 软件包
bash sapi/quickstart/windows/native-build/msys2/msys2-install-soft.sh

# 下载 visualstudio 2019
bash sapi/quickstart/windows/native-build/msys2/msys2-download-vs-2019.sh

# 准备 PHP 运行时 并执行 composer install
bash sapi/quickstart/windows/native-build/msys2/msys2-download-php-runtime.sh

# 准备PHP SDK
bash sapi/quickstart/windows/native-build/msys2/msys2-download-php-and-php-sdk.sh

# 准备 库
bash sapi/quickstart/windows/native-build/msys2/msys2-download-source-code.sh

# 构建库准备环境依赖
bash sapi/quickstart/windows/native-build/msys2/msys2-download-deps-soft.sh



```

## 实验

```bash
# 下载 visualstudio 2022
bash sapi/quickstart/windows/native-build/msys2/msys2-download-vs-2019.sh
```
