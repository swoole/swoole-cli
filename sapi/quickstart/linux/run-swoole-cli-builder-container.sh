#!/usr/bin/env bash

set -exu
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
    case "$MIRROR" in
    china)
      MIRROR="$2"
      OPTIONS=" --mirror china "
      ;;
    esac

    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

bash setup-php-runtime.sh ${OPTIONS}
export PATH=${__PROJECT__}/bin/runtime:$PATH
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem "

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
