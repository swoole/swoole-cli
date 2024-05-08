#!/usr/bin/env bash

# 更新源
pacman -Syy --noconfirm
# 无须确认安装包
pacman -Syy --noconfirm git curl


curl -Lo VisualStudioSetup.exe 'https://c2rsetup.officeapps.live.com/c2r/downloadVS.aspx?sku=community&channel=Release&version=VS2022'
# curl -Lo VisualStudioSetup.exe 'https://aka.ms/vs/17/release/vs_community.exe'
# curl -Lo vs_buildtools.exe 'https://aka.ms/vs/17/release/vs_buildtools.exe'




git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git
git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git
