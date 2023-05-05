#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}


set -x

curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
# 使用镜像
# sh get-docker.sh --mirror Aliyun
