#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)

cd ${__PROJECT__}/var


docker save -o "all-dependencies-container-image-$(uname -m).tar" $(cat all-dependencies-container.txt)

# xz 并行压缩 -T cpu核数 -k 保持源文件
xz -9 -T$(nproc) -k "all-dependencies-container-image-$(uname -m).tar"

# xz 解压
# xz -d -T$(nproc) -k "all-dependencies-container-image-$(uname -m).tar.xz"
