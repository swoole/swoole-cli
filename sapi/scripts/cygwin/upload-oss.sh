#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}

exit 0

## tencentyun upload oss
## https://cloud.tencent.com/document/product/436/63144

test -f coscli-windows.exe  ||  wget https://github.com/tencentyun/coscli/releases/download/v0.13.0-beta/coscli-windows.exe

./coscli-windows cp swoole-cli-v".SWOOLE_VERSION."-cygwin-x64  cos://examplebucket-1250000000/test.txt -e cos.ap-chengdu.myqcloud.com


