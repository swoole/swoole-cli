#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../../
  pwd
)
cd ${__PROJECT__}


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

start .
start .
start  "cmd"
start  "cmd"
start  "cmd"
