#!/usr/bin/env bash

set -exu
shopt -s expand_aliases
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=$(
  cd ${__DIR__}/../../../
  pwd
)
cd ${__PROJECT__}

MIRROR=''
OPTIONS=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    case "$MIRROR" in
    china)
      OPTIONS=" --mirror china "
      ;;
    *)
      echo "$0 parameter error"
      exit 0
      ;;
    esac

    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

if [ ! -f ${__PROJECT__}/runtime/php ]; then
  {
    bash setup-php-runtime.sh $OPTIONS
  } || {
    echo $?
  }
fi
export PATH=${__PROJECT__}/runtime:$PATH
alias php="php -d curl.cainfo=${__PROJECT__}/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/runtime/cacert.pem "

export COMPOSER_ALLOW_SUPERUSER=1

if [ "$MIRROR" = 'china' ]; then
  composer config -g repos.packagist composer https://mirrors.tencent.com/composer/
fi

composer install --no-interaction --no-autoloader --no-scripts --prefer-dist -vv --profile # --no-dev
composer dump-autoload --optimize --profile

if [ "$MIRROR" = 'china' ]; then
  composer config -g --unset repos.packagist
fi

php ./prepare.php --skip-download=yes --without-docker=yes

bash make.sh docker-build ${MIRROR}

bash make.sh docker-bash
