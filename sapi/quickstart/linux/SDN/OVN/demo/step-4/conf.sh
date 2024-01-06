#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex



# 路由到 lr02   lr02-join 端口
ovn-nbctl lr-route-add lr01  "0.0.0.0/0" 10.3.20.1

# 路由到 lr01   lr01-join 端口
ovn-nbctl lr-route-add lr02 "10.1.20.0/24" 10.2.20.1
