#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

ovn-nbctl --if-exists ls-del ls01
ovn-nbctl ls-add ls01


ovn-nbctl lsp-add ls01 ls01_port02
ovn-nbctl lsp-set-addresses ls01_port02 '00:02:00:00:00:02'
ovn-nbctl lsp-set-port-security ls01_port02  '00:02:00:00:00:02'


#添加第二个 logical port
ovn-nbctl lsp-add ls01 ls01_port03
ovn-nbctl lsp-set-addresses ls01_port03 '00:02:00:00:00:03'
ovn-nbctl lsp-set-port-security ls01_port03 '00:02:00:00:00:03'

ovn-nbctl show
