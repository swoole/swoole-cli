#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}

test -d php-src && rm -rf php-src
test -d php-sdk-2.2.0 && rm -rf php-sdk-binary-tools


git clone -b php-8.3.6     --depth=1 https://github.com/php/php-src.git
git clone -b php-sdk-2.2.0 --depth=1 https://github.com/php/php-sdk-binary-tools.git

