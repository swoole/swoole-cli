#!/usr/bin/env bash
set -x

__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../../../
  pwd
)
cd ${__PROJECT__}

# https://windows.php.net/downloads/releases/archives/

test -d php/ && rm -rf php/
test -f php-8.2.19-nts-Win32-vs16-x64.zip ||  curl -Lo php-8.2.19-nts-Win32-vs16-x64.zip https://windows.php.net/downloads/releases/archives/php-8.2.19-nts-Win32-vs16-x64.zip

unzip -d php php-8.2.19-nts-Win32-vs16-x64.zip

pwd
cp -f php/php.ini-production php/php.ini

mkdir -p bin/runtime/
test -f bin/runtime/composer.phar || curl -Lo bin/runtime/composer.phar https://getcomposer.org/download/latest-stable/composer.phar
export PATH=$PATH:${__PROJECT__}/php/

php -v

df -h /
PHP_EXT_DIR=''
if [[ -n  "$GITHUB_WORKSPACE" ]] && [[ -n "$GITHUB_ACTION" ]] ; then
  PHP_EXT_DIR=${GITHUB_WORKSPACE}'\php\ext\'
else
  DISK_DRIVE=$( df -h / | sed -n '2p' | awk '{ print $1 }' )$(pwd)
  echo $DISK_DRIVE
  WIND_DIR=$(echo $DISK_DRIVE | sed 's/\//\\/g')

  # PHP_EXT_DIR='C:\msys64\home\Administrator\swoole-cli\php\ext\'
  PHP_EXT_DIR=${WIND_DIR}'\php\ext\'
  echo $PHP_EXT_DIR
fi

while [ $# -gt 0 ]; do
  case "$1" in
  --php-ext-dir)
    PHP_EXT_DIR="$2"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

#echo 'extension_dir=C:\msys64\home\Administrator\swoole-cli\php\ext\' >> php/php.ini
echo "extension_dir=${PHP_EXT_DIR}" >> php/php.ini
echo 'extension=php_curl.dll' >> php/php.ini
echo 'extension=php_bz2.dll'  >> php/php.ini
echo 'extension=php_openssl.dll' >>  php/php.ini
echo 'extension=php_fileinfo.dll' >> php/php.ini

php -v
php -m
php bin/runtime/composer.phar install  --no-interaction --no-autoloader --no-scripts --profile --ignore-platform-req=ext-posix --ignore-platform-req=ext-yaml
php bin/runtime/composer.phar dump-autoload --optimize --profile --ignore-platform-req=ext-posix --ignore-platform-req=ext-yaml
