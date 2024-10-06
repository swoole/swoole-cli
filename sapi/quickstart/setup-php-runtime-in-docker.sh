#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
if [ -f ${__DIR__}/prepare.php ] ; then
  __PROJECT__=$(
    cd ${__DIR__}/
    pwd
  )
else
  __PROJECT__=$(
    cd ${__DIR__}/../../
    pwd
  )
fi

cd ${__DIR__}

sh setup-php-runtime.sh

# 容器内
if [ -f /.dockerenv ];then

  export PATH="${__PROJECT__}/bin/runtime:$PATH"

  cd ${__PROJECT__}/var/runtime

  cp -f swoole-cli /usr/local/bin/
  cp -f composer.phar /usr/local/bin/

  ln -sf /usr/local/bin/swoole-cli /usr/local/bin/php
  ln -sf /usr/local/bin/composer.phar /usr/local/bin/composer

  cd ${__PROJECT__}/
  php -v
  compoer list

fi
