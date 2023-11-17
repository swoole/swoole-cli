

# CNI calico
# https://docs.tigera.io/calico/latest/getting-started/kubernetes/self-managed-onprem/onpremises
curl https://raw.githubusercontent.com/projectcalico/calico/v3.26.3/manifests/calico.yaml -O

kubectl create -f calico.yaml
