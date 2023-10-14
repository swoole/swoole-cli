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

DOWNLOAD_BOX_DIR=${__PROJECT__}/var/download-box/
mkdir -p "${DOWNLOAD_BOX_DIR}"
mkdir -p "${DOWNLOAD_BOX_DIR}/lib/"
mkdir -p "${DOWNLOAD_BOX_DIR}/ext/"

cd "${DOWNLOAD_BOX_DIR}"

cd ${__PROJECT__}/

mkdir -p pool/lib
mkdir -p pool/ext


awk 'BEGIN { cmd="cp -ri var/download-box/lib/* pool/lib"  ; print "n" |cmd; }'
awk 'BEGIN { cmd="cp -ri var/download-box/ext/* pool/ext"; print "n" |cmd; }'

cd ${__PROJECT__}


