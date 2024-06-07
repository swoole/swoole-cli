#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__DIR__}

sh setup-php-runtime.sh

export PATH="${__PROJECT__}/bin/runtime:$PATH"

cd ${__PROJECT__}/var/runtime

cp -f swoole-cli /usr/local/bin/
cp -f composer.phar /usr/local/bin/

ln -sf /usr/local/bin/swoole-cli /usr/local/bin/php
ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer

cd ${__PROJECT__}/
php -v
