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

if [ ! -f "${__PROJECT__}/bin/runtime/php" ] ;then
  bash sapi/quickstart/setup-php-runtime.sh --mirror china
fi


export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"

php -v

export COMPOSER_ALLOW_SUPERUSER=1
#composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
composer update  --optimize-autoloader
composer config -g --unset repos.packagist

# 可用配置参数
# --with-swoole-pgsql=1
# --with-global-prefix=/usr/local/swoole-cli
# --with-dependency-graph=1
# --with-web-ui
# --with-build-type=dev
# --with-skip-download=1
# --with-http-proxy=http://192.168.3.26:8015
# --conf-path="./conf.d.extra"
# --without-docker=1
# @macos
# --with-override-default-enabled-ext=1
# --with-php-version=8.1.20
# --with-c-compiler=[gcc|clang] 默认clang



# bash sapi/quickstart/mark-install-library-cached.sh
bash sapi/quickstart/clean-folder.sh

php prepare.php \
  --with-global-prefix=/usr/local/swoole-cli \
  --with-install-library-cached=1 \
  +common


bash make-install-deps.sh

bash make.sh all-library

bash make.sh config

bash make.sh build





exit 0





SYSTEM=`uname -s 2>/dev/null`
RELEASE=`uname -r 2>/dev/null`
MACHINE=`uname -m 2>/dev/null`
PLATFORM="$SYSTEM:$RELEASE:$MACHINE";
PLATFORM="$SYSTEM:$MACHINE";
echo $PLATFORM

which php
composer suggests --all
composer dump-autoload



:<<'EOF'
cho -e "Enter numbers 1-4" \c"
read NUM
case $NUM in
    1) echo "one";;
    2) echo "two";;
    3) echo "three";;
    4) echo "four";;
    *) echo "invalid answer"
       exit 1;;
esac
EOF




