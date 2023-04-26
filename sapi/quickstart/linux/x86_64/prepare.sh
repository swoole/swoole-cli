#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}

export PATH=${__PROJECT__}/bin/runtime:$PATH

# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer update

php prepare.php  --with-build-type=release +apcu +ds
