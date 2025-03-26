#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}
cd ${__PROJECT__}
if [ -f bin/runtime/node/bin/node ] ; then
  bash setup-nodejs-runtime.sh --mirror china
fi

export PATH="${__PROJECT__}/bin/runtime/node/bin/:$PATH"

npm install pnpm --registry=https://registry.npmmirror.com

bash  sync-frontend-library.sh
