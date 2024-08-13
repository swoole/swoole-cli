#!/usr/bin/env bash

set -x
# kubectl drain <node name> --delete-emptydir-data --force --ignore-daemonsets

# kubectl -n kube-system get cm kubeadm-config -o yaml

{ kubeadm reset; } || { echo $?; }

iptables -F && iptables -t nat -F && iptables -t mangle -F && iptables -X

ipvsadm -C
ipvsadm --clear
test -f $HOME/.kube/config && rm -f $HOME/.kube/config

# reboot
