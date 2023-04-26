#!/bin/bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../
  pwd
)
cd ${__PROJECT__}


mkdir -p  ${__PROJECT__}/bin/runtime
mkdir -p  ${__PROJECT__}/var

cd ${__PROJECT__}/var


SWOOLE_CLI_DOWNLOAD_URL="https://github.com/swoole/swoole-src/releases/download/v5.0.1/swoole-cli-v5.0.1-macos-x64.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"


mirror=''
while [ $# -gt 0 ]; do
	case "$1" in
		--mirror)
			mirror="$2"
			shift
			;;
		--*)
			echo "Illegal option $1"
			;;
	esac
	shift $(( $# > 0 ? 1 : 0 ))
done

case "$mirror" in
	china)
    SWOOLE_CLI_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/swoole-cli-v5.0.1-macos-x64.tar.xz"
    COMPOSER_DOWNLOAD_URL="https://mirrors.aliyun.com/composer/composer.phar"
		;;

esac



test -f swoole-cli-v5.0.1-macos-x64.tar.xz || wget -O swoole-cli-v5.0.1-macos-x64.tar.xz ${SWOOLE_CLI_DOWNLOAD_URL}
test -f swoole-cli-v5.0.1-macos-x64.tar ||  xz -d -k swoole-cli-v5.0.1-macos-x64.tar.xz

test -f swoole-cli ||  tar -xvf  swoole-cli-v5.0.1-macos-x64.tar
chmod a+x swoole-cli

test -f composer.phar ||  wget -O composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar

mv ${__PROJECT__}/var/swoole-cli ${__PROJECT__}/bin/runtime/php
mv ${__PROJECT__}/var/composer.phar ${__PROJECT__}/bin/runtime/composer
echo " use  php rumtime :"
echo "export PATH=${__PROJECT__}/bin/runtime:\$PATH"
echo " "
export PATH=${__PROJECT__}/bin/runtime:$PATH