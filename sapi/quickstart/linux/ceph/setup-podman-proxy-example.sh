
set -x

export HTTP_PROXY=http://192.168.3.26:8015
export HTTPS_PROXY=http://192.168.3.26:8015
export NO_PROXY=0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,127.0.0.0/8,172.16.0.0/12,192.168.0.0/16,localhost,.aliyuncs.com


podman pull registry.k8s.io/pause:3.9

#  docker.io (docker hub公共镜像库)
#  gcr.io (Google container registry)
#  registry.k8s.io (等同于gcr.io/google-containers)
#  quay.io (Red Hat运营的镜像库)
#  ghcr.io (github 运营的镜像库)
