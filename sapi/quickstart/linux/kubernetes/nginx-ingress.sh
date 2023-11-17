
# nginx ingress
# https://kubernetes.github.io/ingress-nginx/deploy/#bare-metal-clusters

curl  -o nginx-ingress-deploy.yaml  https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.8.2/deploy/static/provider/baremetal/deploy.yaml

kubectl create -f nginx-ingress-deploy.yaml
