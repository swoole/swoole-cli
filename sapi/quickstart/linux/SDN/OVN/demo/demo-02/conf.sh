#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex


ovn-nbctl --if-exists lsp-del ls01-lr01

ovn-nbctl lsp-add ls01 ls01-lr01
ovn-nbctl lsp-set-type ls01-lr01 router
ovn-nbctl lsp-set-addresses ls01-lr01 router
ovn-nbctl lsp-set-options ls01-lr01 router-port=lr01-ls01

ovn-nbctl --if-exists lr-del lr01
ovn-nbctl lr-add lr01

ovn-nbctl lrp-add lr01 lr01-ls01   ee:ee:01:00:00:01 10.1.20.1/24






ovn-nbctl show
