#!/bin/bash
set -exu

__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}

sudo mkdir -p /etc/wireguard/
sudo cp -f configuration.conf  /etc/wireguard/wg0.conf
wg-quick up  wg0

sudo wg show
