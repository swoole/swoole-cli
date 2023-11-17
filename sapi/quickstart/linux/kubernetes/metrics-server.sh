
curl -L -o kubernetes-metrics-server.yaml  https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml

kubectl create -f kubernetes-metrics-server.yaml
