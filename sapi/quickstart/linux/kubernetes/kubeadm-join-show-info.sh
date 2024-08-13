#!/usr/bin/env bash

JOIN_TOKEN=$(openssl x509 -pubkey -in /etc/kubernetes/pki/ca.crt | openssl rsa -pubin -outform der 2>/dev/null | openssl dgst -sha256 -hex | sed 's/^.* //')

# kubeadm join --token <token> <control-plane-host>:<control-plane-port> --discovery-token-ca-cert-hash sha256:<hash>

kubeadm token list

echo "kubeadm join --token ${JOIN_TOKEN} control-plane-endpoint-api.intranet.jingjingxyk.com:6443 --discovery-token-ca-cert-hash sha256:<hash>"
