#!/bin/bash

set -exu
__CURRENT__=`pwd`
__DIR__=$(cd "$(dirname "$0")";pwd)
cd ${__DIR__}/

# apt install -y socat tini
# pip3 install  --upgrade supervisor
# pip3 更新所有包
# pip3 list --outdated --format=freeze | grep -v '^\-e' | cut -d = -f 1  | xargs -n1 pip3 install -U


# sed -i "s@deb.debian.org@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list
# sed -i "s@security.debian.org@mirrors.tuna.tsinghua.edu.cn@g" /etc/apt/sources.list

#优先使用
#sed -i "s@deb.debian.org@mirrors.aliyun.com@g" /etc/apt/sources.list
#sed -i "s@security.debian.org@mirrors.aliyun.com@g" /etc/apt/sources.list



apt update -y
apt install -y  python3 python3-pip

# apt install -y  python3 python3-pip socat tini  uuid uuid-runtime wget curl procps sudo
# apt install -y  gettext  procps  lsof  dnsutils iproute2 net-tools vim iputils-ping
# apt install -y  privoxy  proxychains

pip3 config set global.index-url https://pypi.tuna.tsinghua.edu.cn/simple
pip3 install supervisor

# 生成 supervisor 默认配置文件
test -d /etc/supervisord.d/user-custom/ || mkdir -p /etc/supervisord.d/user-custom/
# 创建supervisord配置文件

if test ! -f /etc/supervisord.conf
then
{
  echo_supervisord_conf > /etc/supervisord.conf
  cat  >> /etc/supervisord.conf <<EOF
[include]
files = /etc/supervisord.d/user-custom/*.conf

EOF


}
fi

num=$(ps -ef | grep  '/usr/local/bin/supervisord' | grep -v 'grep' | wc -l)
if test $num -eq 0
then
{
  supervisord -c /etc/supervisord.conf
}
else
{
  echo 'supervisor ready running !'
}
fi


