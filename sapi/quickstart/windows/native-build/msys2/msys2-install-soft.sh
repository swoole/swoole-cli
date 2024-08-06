#!/usr/bin/env bash

# 更新源
pacman -Syy --noconfirm
# 无须确认安装包
pacman -Syy --noconfirm git curl wget openssl zip unzip xz  lzip

# pacman -Syy --noconfirm binutils

