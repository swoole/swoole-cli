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

# 可用配置参数
# --with-swoole-pgsql=1
# --with-global-prefix=/usr/local/swoole-cli
# --with-dependency-graph=1
# --with-web-ui
# --with-build-type=dev
# --with-skip-download=1
# --with-install-library-cached=1
# --with-http-proxy=http://192.168.3.26:8015
# --conf-path="./conf.d.extra"
#  --without-docker=1
# @macos
# --with-override-default-enabled-ext=1
# --with-php-version=8.1.20
# --with-c-compiler=[gcc|clang] 默认clang
# --conf-path="./conf.d.extra"


bash sapi/quickstart/mark-install-library-cached.sh

php prepare.php \
  --with-global-prefix=/usr/local/swoole-cli \
  --with-install-library-cached=1 \
  +inotify +apcu +ds +xlswriter +ssh2 +pgsql +pdo_pgsql \
  --with-swoole-pgsql=1 --with-libavif=1


bash make-install-deps.sh

bash make.sh all-library

bash make.sh config

bash make.sh build
