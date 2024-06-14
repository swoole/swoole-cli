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


test -d php-sdk-binary-tools ||  git clone -b master --depth=1 https://github.com/php/php-sdk-binary-tools.git
test -d php-src || git clone -b php-8.3.7 --depth=1 https://github.com/php/php-src.git

cp -rf ext/* php-src/ext/
ls -lh php-src/ext/
