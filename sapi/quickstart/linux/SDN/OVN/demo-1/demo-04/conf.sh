#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex


# 准备链接外部网路路由配置


ovn-nbctl --if-exists lrp-del lr02-public
ovn-nbctl lrp-add lr02 lr02-public  ee:ee:04:00:00:06 10.4.20.1/24



ovn-nbctl --if-exists ls-del  public
ovn-nbctl ls-add public

ovn-nbctl lsp-add public public-lr02
ovn-nbctl lsp-set-type public-lr02 router
ovn-nbctl lsp-set-addresses public-lr02 router
ovn-nbctl lsp-set-options public-lr02 router-port=lr02-public

# connecton 物理网络
# Create a localnet port
ovn-nbctl lsp-add public public_external_port
ovn-nbctl lsp-set-type public_external_port localnet
ovn-nbctl lsp-set-addresses public_external_port unknown
ovn-nbctl lsp-set-options public_external_port network_name=external-network-provider


ovn-nbctl show
