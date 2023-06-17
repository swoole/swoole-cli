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

if [[ -f /.dockerenv ]]; then
  echo 'no running in docker'
  exit 0
fi

mkdir -p ${__PROJECT__}/var
cd ${__PROJECT__}/var

if [[ ! -f all-dependencies-container.txt ]]; then
  echo 'no all-dependencies-container.txt file'
  exit 0
fi

IMAGE=$(cat all-dependencies-container.txt)

MIRROR_IMAGE=$(echo ${IMAGE} | sed 's@docker.io/phpswoole/swoole-cli-builder@registry-vpc.cn-beijing.aliyuncs.com/jingjingxyk-public/app@')

docker tag ${IMAGE} ${MIRROR_IMAGE}

docker push ${MIRROR_IMAGE}
