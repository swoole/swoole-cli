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
WITH_PHP_COMPOSER=1

# 配置系统仓库  china mirror
MIRROR='china'



OPTIONS=''

while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},127.0.0.1,localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com"
    NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn"
    NO_PROXY="${NO_PROXY},.tencent.com"
    NO_PROXY="${NO_PROXY},.sourceforge.net"
    export NO_PROXY="${NO_PROXY},.npmmirror.com"

    OPTIONS="${OPTIONS} --with-http-proxy=${HTTP_PROXY}  "
    ;;
  --debug)
    set -x
    OPTIONS="${OPTIONS}  --with-build-type=debug "
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
                sh sapi/quickstart/linux/debian-init.sh --mirror china
            else
                sh sapi/quickstart/linux/debian-init.sh
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
        OPTIONS="${OPTIONS} --without-docker=1  "
    fi
fi

if [ "$OS" = 'macos' ] ; then
  number=$(which flex  | wc -l)
  if test $number -eq 0 ; then
  {
        if [ "$MIRROR" = 'china' ] ; then
            bash sapi/quickstart/macos/macos-init.sh --mirror china
        else
            bash sapi/quickstart/macos/macos-init.sh
        fi
  }
  fi
fi

bash sapi/quickstart/clean-folder.sh

php -v


if [ ${WITH_PHP_COMPOSER} -eq 1 ] ; then
    export COMPOSER_ALLOW_SUPERUSER=1
    if [ "$MIRROR" = 'china' ]; then
        composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
        # composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
    else
        composer config -g repos.packagist composer https://packagist.org
    fi
    # composer suggests --all
    # composer dump-autoload

    # composer update  --optimize-autoloader
    composer config -g --unset repos.packagist
fi


# 定制构建选项
OPTIONS=' --with-override-default-enabled-ext=1 '
OPTIONS="${OPTIONS} +musl_cross_make"
OPTIONS="${OPTIONS} --with-http-proxy=http://127.0.0.1:8010"
OPTIONS="${OPTIONS} --with-c-compiler=gcc "


if [ ${IN_DOCKER} -eq 1 ] ; then
{
# 容器中
  php prepare.php  ${OPTIONS}

} else {
# 容器外
  php prepare.php --without-docker=1  ${OPTIONS}

}
fi


if [ "$OS" = 'linux' ]; then
  bash make-install-deps.sh
  bash make.sh musl_cross_make

  exit 0
fi

