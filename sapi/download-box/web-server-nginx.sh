#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__PROJECT__}

DOCUMENT_ROOT=${__PROJECT__}/pool/

IMAGE=nginx:alpine

{
  docker stop download-box-web-server
  sleep 5
} || {
  echo $?
}

docker run -d --rm --name download-box-web-server \
  -p 9503:80 \
  -v ${DOCUMENT_ROOT}:/usr/share/nginx/html/ \
  -v ${__DIR__}/default.conf:/etc/nginx/conf.d/default.conf \
  ${IMAGE}
