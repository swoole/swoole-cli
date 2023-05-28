#!/bin/bash


__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../
  pwd
)
cd ${__DIR__}

if [ ! -f ${__PROJECT__}/var/runtime/swoole-cli ];then
    echo '   please run  setup-php-runtime.sh '
    exit 0
fi
set -exu
export PATH="${__PROJECT__}/bin/runtime:$PATH"

cd ${__PROJECT__}/var/runtime

cp -f swoole-cli /usr/local/bin/
cp -f composer.phar /usr/local/bin/

ln -sf /usr/local/bin/swoole-cli /usr/local/bin/php
ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer

cd ${__PROJECT__}/
php -v
