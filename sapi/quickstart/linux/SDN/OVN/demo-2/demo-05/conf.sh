#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

# 出网路由配置

ovn-nbctl lrp-set-gateway-chassis lr03-public "b9d189c3-5107-40c5-97f0-0dc96f5547e9" 20




ovn-nbctl lrp-get-gateway-chassis lr03-public
ovn-nbctl list gateway_chassis

exit 0
ovn-nbctl lrp-del-gateway-chassis lr03-public "b9d189c3-5107-40c5-97f0-0dc96f5547e9"
ovn-nbctl lrp-del-gateway-chassis lr03-public "d8a2d5fe-b505-411e-85e8-282cf8233536"
