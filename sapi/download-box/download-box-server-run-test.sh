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

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/

if [ -f "${DOWNLOAD_BOX_DIR}/download-box.txt" ]; then
  {
    IMAGE=$(head -n 1 "${DOWNLOAD_BOX_DIR}/download-box.txt")

    {
      docker stop download-box-web-server
      sleep 5
    } || {
      echo $?
    }
    docker run -d --rm --name download-box-web-server -p 9503:80 ${IMAGE}
    echo " browser open  http://0.0.0.0:9503 "
  }
else
  {
    echo 'no found container download-box.txt '
    exit 0
  }
fi
