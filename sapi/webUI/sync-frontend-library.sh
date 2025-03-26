#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}

cd ${__PROJECT__}/


# npm install xterm@3.14.5

mkdir -p public/ajax/libs/xterm/3.14.5/

cp -rf node_modules/xterm/dist/* public/ajax/libs/xterm/3.14.5/
