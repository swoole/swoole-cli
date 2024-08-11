#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}
mkdir -p ${__PROJECT__}/var/
cd ${__PROJECT__}/var/


# docker tini

# https://cloud-atlas.readthedocs.io/zh-cn/latest/docker/init/docker_tini.html


TINI_VERSION v0.19.0
curl -fsSLo tini https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /tini
chmod +x /tini


# ENTRYPOINT ["/tini", "--"]

# Run your program under Tini
# CMD ["/your/program", "-and", "-its", "arguments"]
# or docker run your-image /your/program ...



