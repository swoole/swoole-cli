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

cd ${__PROJECT__}/var/


test -f download_library_use_git.sh && bash download_library_use_git.sh
test -f download_extension_use_git.sh && bash download_extension_use_git.sh


cd ${__PROJECT__}

mkdir -p pool/lib
mkdir -p pool/ext

# cp -rf ${__PROJECT__}/var/download/* ${__PROJECT__}/pool/lib

awk 'BEGIN { cmd="cp -ri var/libraries/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/extensions/* pool/ext"; print "n" |cmd; }'

cd ${__PROJECT__}
