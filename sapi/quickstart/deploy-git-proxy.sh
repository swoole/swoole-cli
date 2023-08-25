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

cp -f ${__PROJECT__}/sapi/quickstart/git-proxy.sh  ${__PROJECT__}/bin/runtime/git-proxy
chmod a+x ${__PROJECT__}/bin/runtime/git-proxy

git config --global core.gitproxy "${__PROJECT__}/bin/runtime/git-proxy"

# git config --global -l
git config  --get --global core.gitproxy
