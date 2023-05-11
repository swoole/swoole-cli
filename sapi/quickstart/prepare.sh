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

OS=$(uname -s)
ARCH=$(uname -m)

export PATH=${__PROJECT__}/bin/runtime:$PATH
php -v

# composer config  repo.packagist composer https://mirrors.aliyun.com/composer/

composer update

# macos
php prepare.php --with-build-type=release +apcu +ds @macos

# linux
php prepare.php --with-build-type=release +apcu +ds -zip -swoole -curl -gd -imagick -mongodb -redis
php prepare.php --with-build-type=release  -zip -swoole -curl -gd -imagick -mongodb -redis -sqlite3 -pdo_sqlite -mysqli -pdo_mysql -soap -xsl -gmp -yaml  +xlswriter
