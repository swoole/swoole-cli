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

if [ -f /.dockerenv ]; then
  git config --global --add safe.directory ${__PROJECT__}
fi


# shellcheck disable=SC2034
OS=$(uname -s)
# shellcheck disable=SC2034
ARCH=$(uname -m)

export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"

php -v

#composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
composer update  --optimize-autoloader
composer config -g --unset repos.packagist

# macos
php prepare.php --with-build-type=release +apcu +ds @macos

# linux
php prepare.php --with-build-type=release +apcu +ds
