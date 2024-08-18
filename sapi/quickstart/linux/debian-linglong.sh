#!/usr/bin/env bash

set -x

# 如意玲珑 新时代Linux桌面应用分发和治理方案
# https://www.linglong.space/
# https://linglong.dev/

apt install -y sudo apt-transport-https ca-certificates curl gpg

apt install -y xdg-utils


# https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/
mkdir -p /etc/apt/keyrings/

curl -fsSL https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/linglong-apt-keyring.gpg

echo "deb [signed-by=/etc/apt/keyrings/linglong-apt-keyring.gpg] https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/ ./" | sudo tee /etc/apt/sources.list.d/linglong.list

# 此方法设置不生效
# sudo bash -c "echo 'deb [trusted=yes] https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/ ./' > /etc/apt/sources.list.d/linglong.list"

# 镜像地址
# https://help.mirrors.cernet.edu.cn/

# gnome-desktop
# apt install task-gnome-desktop

# 玲珑应用商店
# https://store.linglong.dev/

# 玲珑 的诞生
# https://www.deepin.org/zh/deepin-linglong/

sudo apt update -y
sudo apt install -y linglong-builder linglong-box linglong-bin
