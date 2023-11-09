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
mkdir -p ${__PROJECT__}/var/download-box/

cd ${__PROJECT__}/var/download-box/
mkdir -p lib
mkdir -p ext


cd "${__PROJECT__}/"

if [ -f download_library_urls.txt ] && [ -f download_extension_urls.txt ]  ; then
  echo 'downloading source code '
else
  echo 'please run script : '
  echo "cp  build-release-example.sh  build-release.sh "
  echo "prepare.php --skip-download=1 --with-dependency-graph=1 "
  exit 0
fi


cd "${__PROJECT__}/"

sh sapi/scripts/download-dependencies-use-aria2.sh

# 生成扩展依赖图
sh sapi/scripts/generate-dependency-graph.sh
