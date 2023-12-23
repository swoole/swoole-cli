#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex


ovn-nbctl --if-exists lrp-del lr01-lr02
ovn-nbctl  lrp-add lr01 lr01-lr02   ee:ee:03:00:00:02 10.3.20.2/24 peer=lr02-lr01

ovn-nbctl --if-exists lr-del lr02
ovn-nbctl lr-add lr02

ovn-nbctl lrp-add lr02 lr02-lr01  ee:ee:03:00:00:03 10.3.20.3/24 peer=lr01-lr02

ovn-nbctl set logical_router lr02 \
 options:chassis="6619c622-be2d-4418-a03a-0fe3b6efe8ce"

# 路由器互联 例子
# ovn-nbctl lrp-add router lr-lr2 52:54:00:c1:69:50 172.16.0.1/24 peer=lr2-lr
# ovn-nbctl lrp-add router2 lr2-lr 52:54:00:c1:70:51 172.16.0.2/24 peer=lr-lr2

