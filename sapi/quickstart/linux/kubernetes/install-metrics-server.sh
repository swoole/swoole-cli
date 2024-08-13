#!/usr/bin/env bash

while [ $# -gt 0 ]; do
  case "$1" in
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    export NO_PROXY="${NO_PROXY}"
    ;;

  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# metrics-server
# https://github.com/kubernetes-sigs/metrics-server/

curl -L -o kubernetes-metrics-server.yaml https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml

kubectl create -f kubernetes-metrics-server.yaml
