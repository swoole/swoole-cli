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

# 添加第六个 logical port
ovn-nbctl lsp-add ls01 ls01_port07
ovn-nbctl lsp-set-addresses ls01_port07 '00:02:00:00:00:07 10.1.20.7'
ovn-nbctl lsp-set-port-security ls01_port07 '00:02:00:00:00:07 10.1.20.7'

# 添加第七个 logical port
ovn-nbctl lsp-add ls01 ls01_port08
ovn-nbctl lsp-set-addresses ls01_port08 '00:02:00:00:00:08 10.1.20.8'
ovn-nbctl lsp-set-port-security ls01_port08 '00:02:00:00:00:08 10.1.20.8'


ovn-nbctl show
