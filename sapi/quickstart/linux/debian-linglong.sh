#!/usr/bin/env bash

set -x

apt install -y sudo apt-transport-https ca-certificates curl gpg
# https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/
apt install -y xdg-utils

mkdir -p /etc/apt/keyrings/

curl -fsSL https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/linglong-apt-keyring.gpg

echo "deb [signed-by=/etc/apt/keyrings/linglong-apt-keyring.gpg] https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/ ./" | sudo tee /etc/apt/sources.list.d/linglong.list

# 此方法设置不生效
# sudo bash -c "echo 'deb [trusted=yes] https://download.opensuse.org/repositories/home:/kamiyadm/Debian_12/ ./' > /etc/apt/sources.list.d/linglong.list"

# 镜像地址
# https://help.mirrors.cernet.edu.cn/

# gnome-desktop
# apt install task-gnome-desktop
