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


mkdir -p  ${__PROJECT__}/var/runtime

cd ${__PROJECT__}/var/runtime

:<<'EOF'

# macOS v5.0.2 有BUG ，打包时 ICU-DATA 没有静态打包
test -f swoole-cli-v5.0.2-macos-x64.tar.xz || wget -O swoole-cli-v5.0.2-macos-x64.tar.xz  https://github.com/swoole/swoole-src/releases/download/v5.0.2/swoole-cli-v5.0.2-macos-x64.tar.xz
test -f  swoole-cli-v5.0.2-macos-x64.tar ||  xz -d -k swoole-cli-v5.0.2-macos-x64.tar.xz
test -f swoole-cli ||  tar -xvf  swoole-cli-v5.0.2-macos-x64.tar
sudo xattr -d com.apple.quarantine  ./swoole-cli
chmod a+x ./swoole-cli

EOF

test -f swoole-cli-v5.0.1-macos-x64.tar.xz || wget -O swoole-cli-v5.0.1-macos-x64.tar.xz  https://github.com/swoole/swoole-src/releases/download/v5.0.1/swoole-cli-v5.0.1-macos-x64.tar.xz
test -f swoole-cli-v5.0.1-macos-x64.tar ||  xz -d -k swoole-cli-v5.0.1-macos-x64.tar.xz
test -f swoole-cli ||  tar -xvf  swoole-cli-v5.0.1-macos-x64.tar
chmod a+x swoole-cli

test -f composer.phar ||  wget -O composer.phar https://getcomposer.org/download/2.5.5/composer.phar
chmod a+x composer.phar

ln -sf ${__PROJECT__}/var/runtime/swoole-cli ${__PROJECT__}/var/runtime/php
ln -sf ${__PROJECT__}/var/runtime/composer.phar ${__PROJECT__}/var/runtime/composer

export PATH=${__PROJECT__}/var/runtime:$PATH