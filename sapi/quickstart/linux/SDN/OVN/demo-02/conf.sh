#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

ovn-nbctl --if-exists ls-del ls01
ovn-nbctl ls-add ls01


ovn-nbctl lsp-add ls01 ls01_port02
ovn-nbctl lsp-set-addresses ls01_port02 '00:02:00:00:00:02 10.1.20.2'
ovn-nbctl lsp-set-port-security ls01_port02  '00:02:00:00:00:02 10.1.20.2'


# 添加第二个 logical port
ovn-nbctl lsp-add ls01 ls01_port03
ovn-nbctl lsp-set-addresses ls01_port03 '00:02:00:00:00:03 10.1.20.3'
ovn-nbctl lsp-set-port-security ls01_port03 '00:02:00:00:00:03 10.1.20.3'

# 添加第三个 logical port
ovn-nbctl lsp-add ls01 ls01_port04
ovn-nbctl lsp-set-addresses ls01_port04 '00:02:00:00:00:04 10.1.20.4'
ovn-nbctl lsp-set-port-security ls01_port04 '00:02:00:00:00:04 10.1.20.4'

# 添加第四个 logical port
ovn-nbctl lsp-add ls01 ls01_port05
ovn-nbctl lsp-set-addresses ls01_port05 '00:02:00:00:00:05 10.1.20.5'
ovn-nbctl lsp-set-port-security ls01_port05 '00:02:00:00:00:05 10.1.20.5'

# 添加第五个 logical port
ovn-nbctl lsp-add ls01 ls01_port06
ovn-nbctl lsp-set-addresses ls01_port06 '00:02:00:00:00:06 10.1.20.6'
ovn-nbctl lsp-set-port-security ls01_port06 '00:02:00:00:00:06 10.1.20.6'


ovn-nbctl lsp-add ls01 ls01-lr01_port01
ovn-nbctl lsp-set-type ls01-lr01_port01 router
ovn-nbctl lsp-set-addresses ls01-lr01_port01 router

ovn-nbctl --if-exists lr-del lr01
ovn-nbctl lr-add lr01

ovn-nbctl lrp-add lr01 lr01-ls01_port01   ee:ee:01:00:00:01 10.1.20.1/24


ovn-nbctl lsp-set-options ls01-lr01_port01 router-port=lr01-ls01_port01



ovn-nbctl show
