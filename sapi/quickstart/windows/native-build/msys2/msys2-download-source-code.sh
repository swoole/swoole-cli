#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../../
  pwd
)
cd ${__PROJECT__}

test -d ext && rm -rf ext

export PATH=$PATH:${__PROJECT__}/php/

php prepare.php --without-docker=1 --skip-download=1 --with-libavif=0 -swoole +swow -uuid +apcu +ds +xlswriter +ssh2

ls -lh var/download-box/

bash var/download-box/download_extension_use_git.sh
bash var/download-box/download_library_use_script_for_windows.sh
bash var/download-box/download_library_use_git.sh
bash sapi/download-box/download-box-dependencies-sync.sh
bash var/download-box/extract-files.sh

