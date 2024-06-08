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

if [ ! -d ext/swoole/.git ] ; then
  git submodule update --init --recursive
fi

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
MIRROR=''

# 依赖库默认安装目录
LIBRARY_INSTALL_PREFIX=/usr/local/swoole-cli

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
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

# 构建环境依赖检查
CMDS_NUMS=0
CMDS=("flex" "pkg-config" "cmake" "re2c" "bison" "curl" "automake" "libtool" "clang" "xz" "zip" "unzip" "autoconf")
CMDS_LEN=${#CMDS[@]}
for cmd in "${CMDS[@]}"; do
    if command -v "$cmd" >/dev/null 2>&1; then
        # echo "$cmd exists"
        ((CMDS_NUMS++))
    fi
done

if [ "$OS" = 'linux' ] ; then
    if [ -f /.dockerenv ]; then
        IN_DOCKER=1
        if test $CMDS_LEN -ne $CMDS_NUMS ;then
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
  if test $CMDS_LEN -ne $CMDS_NUMS ; then
  {
        if [ "$MIRROR" = 'china' ] ; then
            bash sapi/quickstart/macos/macos-init.sh --mirror china
        else
            bash sapi/quickstart/macos/macos-init.sh
        fi
  }
  fi
  OWNER=$(stat -f "%Su" "${LIBRARY_INSTALL_PREFIX}")
  CURRENT_USER=$(whoami)
  if test "${OWNER}" != "${CURRENT_USER}" ; then
    id -u ${CURRENT_USER}
    echo "创建目录： ${LIBRARY_INSTALL_PREFIX} ，并修改所属者为： ${CURRENT_USER} "
    sudo mkdir -p ${LIBRARY_INSTALL_PREFIX}
    CURRENT_USER=$(whoami) && sudo chown -R ${CURRENT_USER}:staff ${LIBRARY_INSTALL_PREFIX}
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
    composer install  --no-interaction --no-autoloader --no-scripts --profile # --no-dev
    composer dump-autoload --optimize --profile

    composer config -g --unset repos.packagist
fi


# 可用配置参数
# --with-swoole-pgsql=1
# --with-global-prefix=/usr/local/swoole-cli
# --with-dependency-graph=1
# --with-web-ui
# --skip-download=1
# --conf-path="./conf.d.extra"
# --without-docker=1
# @macos
# --with-parallel-jobs=8
# --with-download-mirror-url=https://swoole-cli.jingjingxyk.com/


# 定制构建选项
OPTIONS='+apcu +ds +xlswriter +ssh2'
OPTIONS="${OPTIONS} --with-swoole-pgsql=1"
OPTIONS="${OPTIONS} --with-global-prefix=${LIBRARY_INSTALL_PREFIX}"
# OPTIONS="${OPTIONS} @macos"


if [ ${IN_DOCKER} -eq 1 ] ; then
{
# 容器中

  php prepare.php +inotify  ${OPTIONS}

} else {
# 容器外


  php prepare.php --without-docker=1  ${OPTIONS}

}
fi


if [ "$OS" = 'linux'  ] && [ ${IN_DOCKER} -eq 0 ] ; then
   echo ' please run in container !'
   exit 0
fi

bash make.sh all-library

bash make.sh config

bash make.sh build

bash make.sh archive

exit 0






