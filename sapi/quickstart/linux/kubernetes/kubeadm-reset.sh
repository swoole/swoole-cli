#!/usr/bin/env bash

# kubectl drain <node name> --delete-emptydir-data --force --ignore-daemonsets

kubeadm reset

iptables -F && iptables -t nat -F && iptables -t mangle -F && iptables -X

ipvsadm -C
