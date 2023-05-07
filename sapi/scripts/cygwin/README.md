# windows cygwin 构建 swoole-cli

## 准备环境

> 手动安装 cygwin

> 浏览器下载 https://cygwin.com/setup-x86_64.exe 并安装

> 设置软件源 地址： http://mirrors.ustc.edu.cn/cygwin/

> 安装完毕

> windows 开始菜单找到 cygwin64-terminal 并打开

## 构建步骤

```bash

git submodule update --init
bash ./sapi/scripts/cygwin/install-re2c.sh

bash ./sapi/scripts/cygwin/cygwin-config-ext.sh
bash ./sapi/scripts/cygwin/cygwin-config.sh
bash ./sapi/scripts/cygwin/cygwin-build.sh
bash ./sapi/scripts/cygwin/cygwin-archive.sh

```
