#!/usr/bin/env bash

set -eux
__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}

ssh-keygen -t rsa -f id_rsa -N "" -C 'Gernerate SSH Key '

# mkdir -p ~/.ssh/

# ~/.ssh/authorized_keys

# cat id_rsa.pem.pub  >> ~/.ssh/authorized_keys

# vi /etc/ssh/sshd_config
# PubkeyAuthentication yes：启用公钥身份验证；
# PasswordAuthentication no：禁用密码身份验证；


# test link

# ssh -vT git@github.com
# ssh -T  git@gitee.com
# ssh -T  git@gitlab.com
