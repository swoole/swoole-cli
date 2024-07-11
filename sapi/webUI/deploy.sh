#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}
cd ${__PROJECT__}
bash setup-nodejs-runtime.sh

export PATH="${__PROJECT__}/bin/runtime/node/bin/:$PATH"

npm install

bash  sync-frontend-library.sh
