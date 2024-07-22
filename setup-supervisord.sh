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



# systemd  for supervisord
# https://www.freedesktop.org/software/systemd/man/latest/systemd.service.html


# supervisord configure
# http://supervisord.org/configuration.html

  cat  > /etc/supervisord.d/user-custom/example.conf.bak <<'EOF'
[program:example]
command=bash /tmp/example.sh
;process_name=%(program_name)s ; process_name expr (default %(program_name)s)
process_name=example ; process_name expr (default %(program_name)s)
numprocs=1                    ; number of processes copies to start (def 1)
directory=/                ; directory to cwd to before exec (def no cwd)
autostart=true                ; start at supervisord start (default: true)
startsecs=1                   ; # of secs prog must stay up to be running (def. 1)
startretries=3                ; max # of serial start failures when starting (default 3)
autorestart=unexpected        ; when to restart if exited after running (def: unexpected)
exitcodes=0                   ; 'expected' exit codes used with autorestart (default 0)
stopsignal=QUIT               ; signal used to kill process (default TERM)
stopwaitsecs=10               ; max num secs to wait b4 SIGKILL (default 10)
stdout_logfile=/tmp/supervisord-example.log       ; stdout log path, NONE for none; default AUTO
stdout_syslog=false           ; send stdout to syslog with process name (default false)
stderr_logfile=/tmp/supervisord-example.log        ; stderr log path, NONE for none; default AUTO


EOF
