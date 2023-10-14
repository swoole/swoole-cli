#!/bin/bash


__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}

if [ ! -f ${__DIR__}/prepare.php ] ; then
  echo 'no found prepare.php'
  exit 0
fi

cd ${__PROJECT__}

set -x
# shellcheck disable=SC2034
OS=$(uname -s)
# shellcheck disable=SC2034
ARCH=$(uname -m)

case $OS in
'Linux')
  OS="linux"
  ;;
'Darwin')
  OS="macos"
  ;;
*)
  echo '暂未配置的 OS '
  exit 0
  ;;

esac


IN_DOCKER=0


# 配置系统仓库  china mirror
MIRROR='china'
MIRROR=''

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    shift
    ;;
  --proxy)
    export http_proxy="$2"
    export https_proxy="$2"
    shift
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done


if [ "$OS" = 'linux' ] ; then
    if [ -f /.dockerenv ]; then
        IN_DOCKER=1
        number=$(which flex  | wc -l)
        if test $number -eq 0 ;then
        {
            if [ "$MIRROR" = 'china' ] ; then
                sh sapi/quickstart/linux/alpine-init.sh --mirror china
            else
                sh sapi/quickstart/linux/alpine-init.sh
            fi
        }
        fi
        git config --global --add safe.directory ${__PROJECT__}
    else
        # docker inspect -f {{.State.Running}} download-box-web-server
        if [ "`docker inspect -f {{.State.Running}} swoole-cli-builder`" = "true" ]; then
            echo " build container is running "
          else
            echo " build container no running "
        fi
    fi
fi

if [ "$OS" = 'macos' ] ; then
  number=$(which flex  | wc -l)
  if test $number -eq 0 -o -f sapi/quickstart/macos/homebrew-init.sh ;then
  {
        if [ "$MIRROR" = 'china' ] ; then
            bash sapi/quickstart/macos/homebrew-init.sh --mirror china
        else
            bash sapi/quickstart/macos/homebrew-init.sh
        fi
  }
  fi
fi


if [ ! -f "${__PROJECT__}/bin/runtime/php" ] ;then
      if [ "$MIRROR" = 'china' ] ; then
          bash sapi/quickstart/setup-php-runtime.sh --mirror china
      else
          bash sapi/quickstart/setup-php-runtime.sh
      fi
fi

export PATH="${__PROJECT__}/bin/runtime:$PATH"
alias php="php -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem"

php -v

export COMPOSER_ALLOW_SUPERUSER=1
# composer config -g repos.packagist composer https://packagist.org
# composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
if [ "$MIRROR" = 'china' ]; then
    composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
fi
# composer suggests --all
# composer dump-autoload

composer update  --optimize-autoloader
composer config -g --unset repos.packagist


# 可用配置参数
# --with-swoole-pgsql=1
# --with-libavif=1
# --with-global-prefix=/usr/local/swoole-cli
# --with-dependency-graph=1
# --with-web-ui
# --conf-path="./conf.d.extra"
# --without-docker=1
# @macos
# --with-build-type=dev
# --with-skip-download=1
# --with-http-proxy=http://192.168.3.26:8015
# --with-override-default-enabled-ext=0

if [ ${IN_DOCKER} -ne 1 ] ; then
{
# 容器中

  php prepare.php +inotify +apcu +ds +xlswriter +ssh2 +pgsql

} else {
# 容器外

  php prepare.php  +inotify +apcu +ds +xlswriter +ssh2 +pgsql

}
fi

if [ "$OS" = 'linux'  ] && [ ${IN_DOCKER} -eq 0 ] ; then
   echo ' please run in container !'
   exit 0
fi

bash make-install-deps.sh

# 兼容上一版本已构建完毕的依赖库
# bash sapi/quickstart/mark-install-library-cached.sh

bash make.sh all-library

bash make.sh config

bash make.sh build

bash make.sh archive


exit 0


:<<'EOF'
echo  "Enter mirror [china]:\n \c"
read Location
case $Location in
    china)
       echo "use china mirror"
       MIRROR='china'
      ;;

    *) e
      cho " no mirror "
       ;;
esac

EOF





