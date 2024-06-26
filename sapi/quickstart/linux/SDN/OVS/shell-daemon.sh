
# ip netns exec vm1 python3 -m http.server 80 -d
# ip netns exec vm1 nohup  python3 -m http.server 80 -d /tmp/  > /tmp/output.log 2> /tmp/error.log &
nohup ./run > output.log 2> error.log &
