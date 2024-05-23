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

php prepare.php --without-docker=1 --skip-download=1 --with-libavif=1 +uuid +apcu +ds +xlswriter +ssh2

ls -lh var/download-box/

bash var/download-box/download_library_use_script_for_windows.sh
bash var/download-box/download_library_use_git.sh
bash sapi/download-box/download-box-dependencies-sync.sh
bash var/download-box/extract-files.sh

test -d php-sdk-binary-tools ||  git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
test -d php-src || git clone -b php-8.3.7 --depth=1 https://github.com/php/php-src.git

cp -rf ext/* php-src/ext/
ls -lh php-src/ext/
