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


test -f php-8.2.19-nts-Win32-vs16-x64.zip ||  curl -Lo php-8.2.19-nts-Win32-vs16-x64.zip https://windows.php.net/downloads/releases/php-8.2.19-nts-Win32-vs16-x64.zip

unzip -f -d php php-8.2.19-nts-Win32-vs16-x64.zip

pwd
cp -f php/php.ini-production php/php.ini

mkdir -p bin/runtime/
test -f bin/runtime/composer.phar || curl -Lo bin/runtime/composer.phar https://getcomposer.org/download/latest-stable/composer.phar
export PATH=$PATH:${__PROJECT__}/php/

echo $PATH

echo 'extension_dir="C:\msys64\home\Administrator\swoole-cli\php\ext\" ' >> php/php.ini
echo 'extension=php_curl.dll' >> php/php.ini
echo 'extension=php_bz2.dll'  >> php/php.ini
echo 'extension=php_openssl.dll' >>  php/php.ini
echo 'extension=php_fileinfo.dll' >> php/php.ini

php -v
php -m
php bin/runtime/composer.phar install  --no-interaction --no-autoloader --no-scripts --profile --ignore-platform-req=ext-posix --ignore-platform-req=ext-yaml
php bin/runtime/composer.phar dump-autoload --optimize --profile --ignore-platform-req=ext-posix --ignore-platform-req=ext-yaml
