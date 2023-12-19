#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex


ovn-nbctl --if-exists lrp-del lr02-lr03
ovn-nbctl lrp-add lr02 lr02-lr03   ee:ee:03:00:00:04 10.3.20.4/24 peer=lr03-lr02



# 准备链接外部网路路由配置
ovn-nbctl --if-exists lr-del lr03
ovn-nbctl lr-add lr03

ovn-nbctl --if-exists lrp-del lr03-lr02
ovn-nbctl lrp-add lr03 lr03-lr02  ee:ee:03:00:00:05 10.3.20.5/24 peer=lr02-lr03


ovn-nbctl --if-exists lrp-del lr03-public
ovn-nbctl lrp-add lr03 lr03-public  ee:ee:03:00:00:06 10.3.20.244/24



ovn-nbctl --if-exists ls-del  public
ovn-nbctl ls-add public

ovn-nbctl lsp-add public public-lr03
ovn-nbctl lsp-set-type public-lr03 router
ovn-nbctl lsp-set-addresses public-lr03 router
ovn-nbctl lsp-set-options public-lr03 router-port=lr03-public

# connecton 物理网络
# Create a localnet port
ovn-nbctl lsp-add public public_external_port
ovn-nbctl lsp-set-type public_external_port localnet
ovn-nbctl lsp-set-addresses public_external_port unknown
ovn-nbctl lsp-set-options public_external_port network_name=external-network-provider


ovn-nbctl show
