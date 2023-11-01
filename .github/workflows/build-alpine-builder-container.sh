#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}
mkdir -p var/build-github-action-container/
cd ${__PROJECT__}/var/build-github-action-container/

cp -f ${__PROJECT__}/sapi/docker/Dockerfile .
cp -f ${__PROJECT__}/sapi/quickstart/linux/alpine-init.sh .

cat >>Dockerfile<<'EOF'
ADD ./alpine-init.sh /alpine-init.sh
# RUN sh /alpine-init.sh
RUN sh /alpine-init.sh --mirror china
ENTRYPOINT ["tini", "--"]
EOF

IMAGE='swoole-cli-builder:latest'
# docker build -t ${IMAGE} -f ./Dockerfile .
docker build -t ${IMAGE} -f ./Dockerfile . --build-arg="MIRROR=ustc"

docker save -o "swoole-cli-builder-image-$(uname -m).tar" ${IMAGE}
