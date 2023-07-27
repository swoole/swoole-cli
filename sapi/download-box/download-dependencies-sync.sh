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

mkdir -p ${__PROJECT__}/var/
mkdir -p ${__PROJECT__}/var/libraries/
mkdir -p ${__PROJECT__}/var/extensions/

touch ${__PROJECT__}/var/libraries/.gitkeep
touch ${__PROJECT__}/var/extensions/.gitkeep



cd ${__PROJECT__}/

mkdir -p pool/lib
mkdir -p pool/ext

cd ${__PROJECT__}/

# cp -rf ${__PROJECT__}/var/download/* ${__PROJECT__}/pool/lib

awk 'BEGIN { cmd="cp -ri var/libraries/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/extensions/* pool/ext"; print "n" |cmd; }'

cd ${__PROJECT__}


