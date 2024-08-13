#!/usr/bin/env bash

set -x

TOKEN_HASH=$(openssl x509 -pubkey -in /etc/kubernetes/pki/ca.crt | openssl rsa -pubin -outform der 2>/dev/null | openssl dgst -sha256 -hex | sed 's/^.* //')

# kubeadm token create
# kubeadm join --token <token> <control-plane-host>:<control-plane-port> --discovery-token-ca-cert-hash sha256:<hash>

JOIN_TOKEN=$(kubeadm token list | grep 'kubeadm init' | awk '{ print $1}')

# more info
# https://kubernetes.io/docs/setup/production-environment/tools/kubeadm/install-kubeadm/

echo 'swapoff -a '

echo 'kubeadm config images pull --v=5 '
echo "kubeadm join --token ${JOIN_TOKEN} control-plane-endpoint-api.intranet.jingjingxyk.com:6443 --discovery-token-ca-cert-hash sha256:${TOKEN_HASH} --v=5 "
