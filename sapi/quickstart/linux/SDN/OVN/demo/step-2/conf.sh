#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex



ovn-nbctl --if-exists ls-del join
ovn-nbctl ls-add join

ovn-nbctl lsp-add join join-lr01
ovn-nbctl lsp-set-type join-lr01 router
ovn-nbctl lsp-set-addresses join-lr01 router
# ovn-nbctl lsp-set-addresses join-lr01 ee:ee:02:00:00:01
ovn-nbctl lsp-set-options join-lr01 router-port=lr01-join

ovn-nbctl lsp-add join join-lr02
ovn-nbctl lsp-set-type join-lr02 router
ovn-nbctl lsp-set-addresses join-lr02 router
# ovn-nbctl lsp-set-addresses join-lr02 ee:ee:03:00:00:02
ovn-nbctl lsp-set-options join-lr02 router-port=lr02-join




ovn-nbctl --if-exists lrp-del lr01-join
ovn-nbctl lrp-add lr01 lr01-join   ee:ee:02:00:00:01 10.2.20.1/24 peer=lr02-join



ovn-nbctl --if-exists lr-del lr02
ovn-nbctl lr-add lr02
ovn-nbctl --if-exists lrp-del lr02-join
ovn-nbctl  lrp-add lr02 lr02-join  ee:ee:03:00:00:02 10.3.20.1/24 peer=lr01-join



ovn-nbctl set logical_router lr02 options:chassis="9029ec6b-b20d-4df7-aa69-1e7b2cc5588b"


# 路由器互联 例子
# ovn-nbctl lrp-add router lr-lr2 52:54:00:c1:69:50 172.16.0.1/24 peer=lr2-lr
# ovn-nbctl lrp-add router2 lr2-lr 52:54:00:c1:70:51 172.16.0.2/24 peer=lr-lr2

# 移除路由绑定
# ovn-nbctl set logical_router lr02 options:chassis=" "
