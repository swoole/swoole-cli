#!/bin/bash

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}
set -uex

# 去程
ovn-nbctl  lr-route-add lr01 "0.0.0.0/0" 10.1.20.1 lr01-ls01



# 回程




ovn-nbctl show

exit 0

ovn-nbctl  lr-route-del lr01 0.0.0.0/0 10.1.20.1
ovn-nbctl  lr-route-del lr01 10.1.20.1   10.3.20.3  lr01-lr02

ovn-nbctl  lr-route-del lr02 10.3.20.4                 10.3.20.5
ovn-nbctl  lr-route-del lr02 10.3.20.2                 10.3.20.3
ovn-nbctl  lr-route-del lr03 10.3.20.6 10.3.20.7
ovn-nbctl  lr-route-del lr03 10.3.20.244               10.3.20.243


ovn-nbctl lr-route-list lr01
ovn-nbctl lr-route-list lr02
ovn-nbctl lr-route-list lr03
