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

export PATH=${__PROJECT__}/bin/runtime:$PATH
php -v

# composer config  repo.packagist composer https://mirrors.aliyun.com/composer/


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

bash sapi/quickstart/mark-install-library-cached.sh

php prepare.php \
  --with-global-prefix=/usr/local/swoole-cli \
  +inotify +apcu +ds +xlswriter +ssh2 +pgsql +pdo_pgsql \
  --with-swoole-pgsql=1
