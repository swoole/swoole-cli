
cat <<EOF | sudo tee /etc/modules-load.d/k8s.conf
overlay
br_netfilter
EOF

sudo modprobe overlay
sudo modprobe br_netfilter

# 设置所需的 sysctl 参数，参数在重新启动后保持不变
cat <<EOF | sudo tee /etc/sysctl.d/k8s.conf
net.bridge.bridge-nf-call-iptables  = 1
net.bridge.bridge-nf-call-ip6tables = 1
net.ipv4.ip_forward                 = 1
net.ipv6.ip_forward                 = 1
net.ipv6.conf.all.forwarding        = 1
EOF

# 应用 sysctl 参数而不重新启动
sudo sysctl --system



lsmod | grep br_netfilter
lsmod | grep overlay

# 验证设置结果
sysctl net.bridge.bridge-nf-call-iptables net.bridge.bridge-nf-call-ip6tables net.ipv4.ip_forward net.ipv6.conf.all.forwarding



kubeadm config images pull --v=5 --kubernetes-version=$(kubelet --version |  awk -F ' ' '{print $2}')

ip=$(ip address show | grep eth0 | grep 'inet' | awk '{print $2}' | awk -F '/' '{print $1}')
ip=$(ip address show | grep enp0s3 | grep 'inet' | awk '{print $2}' | awk -F '/' '{print $1}')

swapoff -a
export KUBE_PROXY_MODE=ipvs
kubeadm init  \
--kubernetes-version=$(kubelet --version |  awk -F ' ' '{print $2}') \
--pod-network-cidr=10.244.0.0/16,fd00:11::/64 \
--service-cidr=10.96.0.0/16,fd00:22::/112 \
--token-ttl 0 \
--v=5 \
--apiserver-advertise-address="${ip}"
# --control-plane-endpoint='control-plane-endpoint-api.intranet.jingjingxyk.com:6443'
#--apiserver-advertise-address="${ip}"



mkdir -p $HOME/.kube
cp -f  /etc/kubernetes/admin.conf $HOME/.kube/config
chown $(id -u):$(id -g) $HOME/.kube/config


kubectl taint nodes --all node-role.kubernetes.io/control-plane-



#  enable ipvs mod
# kubectl edit configmap kube-proxy -n kube-system
   ## change mode from "" to ipvs
   ## mode: ipvs

ipvsadm -ln


openssl x509 -pubkey -in /etc/kubernetes/pki/ca.crt | openssl rsa -pubin -outform der 2>/dev/null | \
   openssl dgst -sha256 -hex | sed 's/^.* //'




