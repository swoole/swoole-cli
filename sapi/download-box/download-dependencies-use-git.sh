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

cd ${__PROJECT__}/var/

test -f download_library_use_git.sh && bash download_library_use_git.sh
test -f download_extension_use_git.sh && bash download_extension_use_git.sh

cd ${__PROJECT__}

