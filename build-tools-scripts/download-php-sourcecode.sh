#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}
mkdir -p ${__DIR__}/php-versions
cd ${__DIR__}/php-versions

# 下载重试
# curl --connect-timeout 15 --retry 5 --retry-delay 5 -Lo php-8.1.12.tar.gz https://www.php.net/distributions/php-8.1.12.tar.gz

# https://www.php.net/distributions/php-8.2.0.tar.gz

test -d php-8.1.12 && rm -rf php-8.1.12
test -f php-8.1.12.tar.gz || curl -Lo php-8.1.12.tar.gz https://www.php.net/distributions/php-8.1.12.tar.gz
tar -zxvf php-8.1.12.tar.gz


