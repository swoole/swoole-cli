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
cd ${__PROJECT__}

cd ${__PROJECT__}/var

if [ -f download-box.txt ]; then
  {
    IMAGE=$(head -n 1 download-box.txt)
    {
      docker stop download-box
      sleep 5
    } || {
      echo $?
    }
    docker run -d --rm --name download-box -p 8000:80 ${IMAGE}
  }
else
  {
    echo 'no found container download-box.txt '
    exit 0
  }
fi
