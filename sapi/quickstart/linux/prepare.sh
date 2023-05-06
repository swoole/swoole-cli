#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__DIR__}
cd ${__PROJECT__}


set -x


composer suggests
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer update --no-dev  --optimize-autoloader

# sh sapi/download-box/download-box-get-archive-from-server.sh


php prepare.php  +inotify +apcu +ds -mysqli -soap

# build swow
# php prepare.php  +inotify +apcu +ds -swoole +swow
