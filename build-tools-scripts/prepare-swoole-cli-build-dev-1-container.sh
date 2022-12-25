#!/bin/sh

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

export DOCKER_BUILDKIT=1

:<<'EOF'
TIME=`date -u '+%Y%m%dT%H%M%SZ'`
VERSION="build-dev-1-alpine-edge-"${TIME}
IMAGE="docker.io/phpswoole/swoole_cli_os:${VERSION}"
EOF

IMAGE="docker.io/phpswoole/swoole_cli_os:build-dev-1-alpine-edge"


docker build -t ${IMAGE} -f ./Dockerfile-alpine-dev-1  . --progress=plain

# docker push ${IMAGE}


