
# ip netns exec vm1 python3 -m http.server 80 -d
# ip netns exec vm1 nohup  python3 -m http.server 80 -d /tmp/  > /tmp/output.log 2> /tmp/error.log &
nohup ./run > output.log 2> error.log &
# 等同于
nohup ./run > output.log 2>&1 &

# nohup : 不挂断的运行  脱离tty 脱离控制台
# &符号表示将该命令或脚本放入后台运行
