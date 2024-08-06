#!/usr/bin/env bash

set -exu
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
  'MSYS_NT'* | 'CYGWIN_NT'* )
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
'aarch64' | 'arm64' )
  ARCH="arm64"
  ;;
*)
  echo '暂未配置的 ARCH '
  exit 0
  ;;
esac

APP_VERSION='3.0.34'
APP_NAME='privoxy'
VERSION='v1.0.0'

mkdir -p bin/runtime
mkdir -p var/runtime

cd ${__PROJECT__}/var/runtime

APP_DOWNLOAD_URL="https://github.com/jingjingxyk/build-static-privoxy/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
CACERT_DOWNLOAD_URL="https://curl.se/ca/cacert.pem"

if [ $OS = 'windows' ]; then
  APP_DOWNLOAD_URL="https://github.com/jingjingxyk/build-static-privoxy/releases/download/${VERSION}/${APP_NAME}-${APP_VERSION}-vs2022-${ARCH}.zip"
fi

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
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

case "$MIRROR" in
china)
  APP_DOWNLOAD_URL="https://php-cli.jingjingxyk.com/${APP_NAME}-${APP_VERSION}-${OS}-${ARCH}.tar.xz"
  if [ $OS = 'windows' ]; then
    APP_DOWNLOAD_URL="https://php-cli.jingjingxyk.com/${APP_NAME}-${APP_VERSION}-vs2022-${ARCH}.zip"
  fi
  ;;

esac

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
  test -d ${APP_RUNTIME} && rm -rf ${APP_RUNTIME}
  tar -xvf ${APP_RUNTIME}.tar
  chmod a+x privoxy/sbin/privoxy
  mkdir -p ${__PROJECT__}/bin/runtime/${APP_NAME}
  test -d ${__PROJECT__}/bin/runtime/${APP_NAME} && rm -rf ${__PROJECT__}/bin/runtime/${APP_NAME}
  cp -rf ${__PROJECT__}/var/runtime/${APP_NAME}/. ${__PROJECT__}/bin/runtime/${APP_NAME}
fi

cd ${__PROJECT__}/var/runtime

cp -f ${__PROJECT__}/var/runtime/cacert.pem ${__PROJECT__}/bin/runtime/cacert.pem


cd ${__PROJECT__}/


tee ${__PROJECT__}/bin/runtime/privoxy/privoxy-start.sh <<'EOF'
#!/usr/bin/env bash
set -exu
__DIR__=$(
  cd "$(dirname "$0")"
  pwd
)

cd ${__DIR__}/
 ./sbin/privoxy --no-daemon etc/config
EOF


set +x

echo " "
echo " USE PRIVOXY RUNTIME :"
echo " "
echo " change file ./bin/runtime/privoxy/etc/config  "
echo ''
echo ' listen-address  0.0.0.0:8118'
echo ' forward-socks5   /'
echo " confdir ${__PROJECT__}/bin/runtime/privoxy/etc"
echo " logdir ${__PROJECT__}/bin/runtime/privoxy/var/log/privoxy"
echo '#        debug  1'
echo '#        debug  512'
echo '#        debug  1024'
echo ''
echo " cd ./bin/runtime/privoxy "
echo " ./sbin/privoxy --no-daemon etc/config  "
echo ''
echo ' OR '
echo ''
echo ' bash bin/runtime/privoxy/start-privoxy.sh'
echo " "

export PATH="${__PROJECT__}/bin/runtime:$PATH"
