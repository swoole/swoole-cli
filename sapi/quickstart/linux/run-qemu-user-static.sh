#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}



# https://github.com/multiarch/qemu-user-static
# https://hub.docker.com/r/multiarch/qemu-user-static/tags?page=1&name=aarch64

# https://hub.docker.com/_/alpine/tags

docker run --rm --privileged multiarch/qemu-user-static --reset -p yes

#docker run --rm --privileged multiarch/qemu-user-static:x86_64-aarch64
