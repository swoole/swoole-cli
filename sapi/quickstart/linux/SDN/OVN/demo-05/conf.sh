#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

# 出网路由配置

ovn-nbctl lrp-set-gateway-chassis lr03-public "d8a2d5fe-b505-411e-85e8-282cf8233536" 20
# ovn-nbctl lrp-del-gateway-chassis lr03-public lr03-public

ovn-nbctl show
ovn-nbctl list gateway_chassis



# ovn-nbctl create Logical_Router name=router2 options:chassis=e159dc4b-1d96-43b7-bb26-2a9adc01a046
