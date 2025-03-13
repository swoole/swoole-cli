#!/usr/bin/env bash

set -xu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}

cd ${__PROJECT__}

OS=$(uname -s)
ARCH=$(uname -m)

case $OS in
'Linux')
  OS="linux"
  ;;
'Darwin')
  OS="macos"
  ;;
*)
  case $OS in
  'MSYS_NT'*)
    OS="windows"
    ;;
  'MINGW64_NT'*)
    OS="windows"
    ;;
  *)
    echo '暂未配置的 OS '
    exit 0
    ;;
  esac
  ;;
esac

case $ARCH in
'x86_64')
  ARCH="x64"
  ;;
'aarch64' | 'arm64')
  ARCH="arm64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

APP_VERSION='v6.0.0'
APP_NAME='swoole-cli'
VERSION='v6.0.0.0'

MIRROR=''
while [ $# -gt 0 ]; do
  case "$1" in
  --mirror)
    MIRROR="$2"
    ;;
  --proxy)
    export HTTP_PROXY="$2"
    export HTTPS_PROXY="$2"
    NO_PROXY="127.0.0.0/8,10.0.0.0/8,100.64.0.0/10,172.16.0.0/12,192.168.0.0/16"
    NO_PROXY="${NO_PROXY},::1/128,fe80::/10,fd00::/8,ff00::/8"
    NO_PROXY="${NO_PROXY},localhost"
    NO_PROXY="${NO_PROXY},.aliyuncs.com,.aliyun.com,.tencent.com"
    NO_PROXY="${NO_PROXY},.myqcloud.com,.swoole.com"
    export NO_PROXY="${NO_PROXY},.tsinghua.edu.cn,.ustc.edu.cn,.npmmirror.com"
    ;;
  --version)
    # 指定发布 TAG
    if [ $OS = "macos" ]; then
      X_VERSION=$(echo "$2" | grep -E '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
      X_APP_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
    elif [ $OS = "linux" ]; then
      OS_RELEASE=$(awk -F= '/^ID=/{print $2}' /etc/os-release | tr -d '\n' | tr -d '\"')
      if [ "$OS_RELEASE" = 'alpine' ]; then
        X_VERSION=$(echo "$2" | grep -E '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
        X_APP_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}')
      else
        X_VERSION=$(echo "$2" | grep -P '^v\d\.\d{1,2}\.\d{1,2}\.\d{1,2}$')
        X_APP_VERSION=$(echo "$2" | grep -Po '^v\d\.\d{1,2}\.\d{1,2}')
      fi
    else
      X_VERSION=''
      X_APP_VERSION=''
    fi

    if [[ -n $X_VERSION ]] && [[ -n $X_APP_VERSION ]]; then
      {
        VERSION=$X_VERSION
        APP_VERSION=$X_APP_VERSION
      }
    fi
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

mkdir -p bin/runtime
mkdir -p var/runtime

cd ${__PROJECT__}/var/runtime

APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"
CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://github.com/swoole/swoole-cli/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"
fi

case "$MIRROR" in
china)
  APP_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  COMPOSER_DOWNLOAD_URL="https://mirrors.tencent.com/composer/composer.phar"
  if [ $OS = 'windows' ]; then
    APP_DOWNLOAD_URL="https://wenda-1252906962.file.myqcloud.com/dist/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"
  fi
  ;;

esac

test -f composer.phar || curl -fSLo composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar

test -f cacert.pem || curl -fSLo cacert.pem ${CACERT_DOWNLOAD_URL}

APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"

if [ $OS = 'windows' ]; then
  {
    APP_RUNTIME="${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}"
    test -f ${APP_RUNTIME}.zip || curl -fSLo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
    test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
    unzip "${APP_RUNTIME}.zip"
    echo
    exit 0
  }
else
  test -f ${APP_RUNTIME}.tar.xz || curl -fSLo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}
  test -f ${APP_RUNTIME}.tar || xz -d -k ${APP_RUNTIME}.tar.xz
  test -f swoole-cli && rm -f swoole-cli
  tar -xvf ${APP_RUNTIME}.tar
  chmod a+x swoole-cli
  cp -f ${__PROJECT__}/var/runtime/swoole-cli ${__PROJECT__}/bin/runtime/swoole-cli
fi

cd ${__PROJECT__}/var/runtime

cp -f ${__PROJECT__}/var/runtime/composer.phar ${__PROJECT__}/bin/runtime/composer
cp -f ${__PROJECT__}/var/runtime/cacert.pem ${__PROJECT__}/bin/runtime/cacert.pem

cat >${__PROJECT__}/bin/runtime/php.ini <<EOF
curl.cainfo="${__PROJECT__}/bin/runtime/cacert.pem"
openssl.cafile="${__PROJECT__}/bin/runtime/cacert.pem"
swoole.use_shortname=off
display_errors = On
error_reporting = E_ALL

upload_max_filesize="128M"
post_max_size="128M"
memory_limit="1G"
date.timezone="UTC"

opcache.enable=On
opcache.enable_cli=On
opcache.jit=1225
opcache.jit_buffer_size=128M

; jit 更多配置参考 https://mp.weixin.qq.com/s/Tm-6XVGQSlz0vDENLB3ylA

expose_php=Off
apc.enable_cli=1

EOF

cat >${__PROJECT__}/bin/runtime/php-fpm.conf <<'EOF'
; 更多配置参考
; https://github.com/php/php-src/blob/master/sapi/fpm/www.conf.in
; https://github.com/php/php-src/blob/master/sapi/fpm/php-fpm.conf.in

[global]
pid = run/php-fpm.pid
error_log = log/php-fpm.log
daemonize = yes

[www]
user = nobody
group = nobody

listen = 9001
;listen = run/php-fpm.sock

slowlog = log/$pool.log.slow
request_slowlog_timeout = 30s


pm = dynamic
pm.max_children = 5
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3

; MAIN_PID=$(cat var/run/php-fpm.pid)
; 关闭 php-fpm
; kill -QUIT $MAIN_PID

; 平滑重启 php-fpm
; kill -USR2 $MAIN_PID

EOF

cd ${__PROJECT__}/

set +x

echo " "
echo " USE PHP-FPM RUNTIME :"
echo " "
echo "${__PROJECT__}/bin/runtime/swoole-cli -c ${__PROJECT__}/bin/runtime/php.ini -P --fpm-config ${__PROJECT__}/bin/runtime/php-fpm.conf -p ${__PROJECT__}/runtime/var "
echo " "
echo " USE PHP-CLI RUNTIME :"
echo " "
echo " export PATH=\"${__PROJECT__}/bin/runtime:\$PATH\" "
echo " "
echo " shopt -s expand_aliases "
echo " "
echo " alias swoole-cli='swoole-cli -d curl.cainfo=${__PROJECT__}/bin/runtime/cacert.pem -d openssl.cafile=${__PROJECT__}/bin/runtime/cacert.pem' "
echo " OR "
echo " alias swoole-cli='swoole-cli -c ${__PROJECT__}/bin/runtime/php.ini' "
echo " "
test $OS="macos" && echo "sudo xattr -d com.apple.quarantine ${__PROJECT__}/bin/runtime/php"
echo " "
echo " SWOOLE-CLI VERSION  ${APP_VERSION}"
export PATH="${__PROJECT__}/bin/runtime:$PATH"
swoole-cli -v
