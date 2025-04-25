#!/usr/bin/env bash

set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
__PROJECT__=${__DIR__}
shopt -s expand_aliases
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

APP_VERSION='v8.2.28'
APP_NAME='php-fpm'
VERSION='php-fpm-v2.2.0'

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
      X_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}$')
    elif [ $OS = "linux" ]; then
      X_VERSION=$(echo "$2" | grep -Po '^v\d\.\d{1,2}\.\d{1,2}$')
    else
      X_VERSION=''
    fi
    if [[ -n $X_VERSION ]]; then
      {
        VERSION=$X_VERSION
      }
    fi
    ;;
  --php-version)
    # 指定发布 TAG
    if [ $OS = "macos" ]; then
      X_APP_VERSION=$(echo "$2" | grep -Eo '^v\d\.\d{1,2}\.\d{1,2}$')
    elif [ $OS = "linux" ]; then
      X_APP_VERSION=$(echo "$2" | grep -Po '^v\d\.\d{1,2}\.\d{1,2}$')
    else
      X_VERSION=''
    fi
    if [[ -n $X_APP_VERSION ]]; then
      {
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

cd ${__PROJECT__}
mkdir -p runtime/
mkdir -p var/runtime
APP_RUNTIME_DIR=${__PROJECT__}/runtime/${APP_NAME}
mkdir -p ${APP_RUNTIME_DIR}

cd ${__PROJECT__}/var/runtime

APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
COMPOSER_DOWNLOAD_URL="https://getcomposer.org/download/latest-stable/composer.phar"
CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://github.com/swoole/build-static-php/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-vs2022-${ARCH}.zip"
fi

case "$MIRROR" in
china)
  APP_DOWNLOAD_URL="https://php-cli.jingjingxyk.com/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  COMPOSER_DOWNLOAD_URL="https://mirrors.tencent.com/composer/composer.phar"
  if [ $OS = 'windows' ]; then
    APP_DOWNLOAD_URL="https://php-cli.jingjingxyk.com/${APP_NAME}-${APP_VERSION}-cygwin-${ARCH}.zip"
  fi
  ;;

esac

test -f composer.phar || curl -LSo composer.phar ${COMPOSER_DOWNLOAD_URL}
chmod a+x composer.phar

test -f cacert.pem || curl -LSo cacert.pem ${CACERT_DOWNLOAD_URL}

APP_RUNTIME="${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}"

if [ $OS = 'windows' ]; then
  {
    APP_RUNTIME="${APP_NAME}-${APP_VERSION}-vs2022-${ARCH}"
    test -f ${APP_RUNTIME}.zip || curl -LSo ${APP_RUNTIME}.zip ${APP_DOWNLOAD_URL}
    test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
    unzip "${APP_RUNTIME}.zip"
    exit 0
  }
else
  test -f ${APP_RUNTIME}.tar.xz || curl -LSo ${APP_RUNTIME}.tar.xz ${APP_DOWNLOAD_URL}
  test -f ${APP_RUNTIME}.tar || xz -d -k ${APP_RUNTIME}.tar.xz
  test -f php-fpm || tar -xvf ${APP_RUNTIME}.tar
  chmod a+x php-fpm
  cp -f ${__PROJECT__}/var/runtime/php-fpm ${APP_RUNTIME_DIR}/
  mkdir -p ${APP_RUNTIME_DIR}/log
  mkdir -p ${APP_RUNTIME_DIR}/run
  mkdir -p ${APP_RUNTIME_DIR}/var
fi

cd ${__PROJECT__}/var/runtime

cp -f ${__PROJECT__}/var/runtime/composer.phar ${APP_RUNTIME_DIR}/composer
cp -f ${__PROJECT__}/var/runtime/cacert.pem ${APP_RUNTIME_DIR}/cacert.pem

cat >${APP_RUNTIME_DIR}/php.ini <<EOF
curl.cainfo="${APP_RUNTIME_DIR}/cacert.pem"
openssl.cafile="${APP_RUNTIME_DIR}/cacert.pem"
swoole.use_shortname=off
display_errors = On
error_reporting = E_ALL

upload_max_filesize="128M"
post_max_size="128M"
memory_limit="1G"
date.timezone="UTC"

opcache.enable_cli=1
opcache.jit=1254
opcache.jit_buffer_size=480M

expose_php=Off

EOF

cat >${APP_RUNTIME_DIR}/php-fpm.conf <<'EOF'
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

tee ${APP_RUNTIME_DIR}/start.sh <<'EOF'
#!/usr/bin/env bash
set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}
mkdir -p log
mkdir -p run
mkdir -p var


OS=$(uname -s)
if [ "$OS" = 'Darwin' ]; then
  # 非 root 设置此项无效
  USER=$(whoami)
  GROUP=$(groups $(whoami) | cut -d' ' -f1)
  # chown -R $(whoami):staff
  sed -i.bak "s/user = nobody/user = ${USER}/" php-fpm.conf
  sed -i.bak "s/group = nogroup/group = ${GROUP}/" php-fpm.conf
  test -f php-fpm.conf.bak && rm -f php-fpm.conf.bak
fi

${__DIR__}/php-fpm -c ${__DIR__}/php.ini --fpm-config ${__DIR__}/php-fpm.conf -p ${__DIR__}/var

EOF

tee ${APP_RUNTIME_DIR}/reload.sh <<'EOF'
#!/usr/bin/env bash
set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

kill -USR2 $(cat ${__DIR__}/var/run/php-fpm.pid)

EOF

tee ${APP_RUNTIME_DIR}/stop.sh <<'EOF'
#!/usr/bin/env bash
set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)
cd ${__DIR__}

kill -QUIT $(cat ${__DIR__}/var/run/php-fpm.pid)


EOF

set +x

echo " "
echo " USE PHP-FPM :"
echo " "
echo " export PATH=\"${APP_RUNTIME_DIR}/:\$PATH\" "
echo " "
echo " php-fpm.conf example  :  https://gitee.com/jingjingxyk/quickstart-nginx/blob/main/php-fpm.example.conf"
echo " "
echo " start php-fpm ${APP_VERSION}"
echo " "
echo " ${APP_RUNTIME_DIR}/php-fpm -c ${APP_RUNTIME_DIR}/php.ini --fpm-config ${APP_RUNTIME_DIR}/php-fpm.conf "
