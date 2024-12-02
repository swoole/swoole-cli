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
mkdir -p "${DOWNLOAD_BOX_DIR}"
mkdir -p "${DOWNLOAD_BOX_DIR}/lib/"
mkdir -p "${DOWNLOAD_BOX_DIR}/ext/"

cd "${DOWNLOAD_BOX_DIR}"

test -f download_library_use_git.sh && bash download_library_use_git.sh
test -f download_extension_use_git.sh && bash download_extension_use_git.sh

cd ${__PROJECT__}
