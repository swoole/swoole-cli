
set -x

systemctl status docker

mkdir -p /lib/systemd/system/docker.service.d/
cat > /lib/systemd/system/docker.service.d/http-proxy.conf <<EOF
[Service]
Environment="HTTP_PROXY=http://192.168.3.26:8015"
Environment="HTTPS_PROXY=http://192.168.3.26:8015"
Environment="NO_PROXY=0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,127.0.0.0/8,172.16.0.0/12,192.168.0.0/16,localhost,.aliyuncs.com,docker.dengxiaci.com:5000"

EOF

systemctl daemon-reload
systemctl restart containerd
