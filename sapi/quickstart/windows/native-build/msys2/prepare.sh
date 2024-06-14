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

# msys2 下载安装  git curl wget openssl zip unzip xz  lzip 软件包
bash sapi/quickstart/windows/native-build/msys2/msys2-install-soft.sh

# 下载 visualstudio 2019
bash sapi/quickstart/windows/native-build/msys2/msys2-download-vs-2019.sh

# 调用 CMD 窗口 安装  vc 运行时
bash sapi/quickstart/windows/native-build/msys2/msys2-install-vc-runtime.sh

start cmd
start cmd
start cmd
