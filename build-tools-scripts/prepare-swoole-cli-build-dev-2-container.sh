#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../
  pwd
)



export DOCKER_BUILDKIT=1
TIME=`date -u '+%Y%m%dT%H%M%SZ'`

VERSION="build-dev-2-alpine-edge-"${TIME}
IMAGE="docker.io/phpswoole/swoole_cli_os:${VERSION}"

cd ${__PROJECT__}
docker build -t ${IMAGE} -f ./Dockerfile  . --progress=plain


cd ${__DIR__}
echo ${IMAGE} > swoole-cli-build-dev-2-container.txt

# docker push ${IMAGE}

exit 0
