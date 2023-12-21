# 交换机绑定端口

````bash

tcpdump -i any -nn port 6081

tcpdump -i any -nnn udp  port 6081


ip netns exec vm1 tcpdump -i vm1 -v -n -l

````
