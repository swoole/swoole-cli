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


PHP_VERSION='8.2.27'
SWOOLE_VERSION='v6.0.1'
X_PHP_VERSION='8.2'

while [ $# -gt 0 ]; do
  case "$1" in
  --php-version)
    PHP_VERSION="$2"
    X_PHP_VERSION=$(echo ${PHP_VERSION:0:3})
    ;;
  --swoole-version)
    SWOOLE_VERSION="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

REDIS_VERSION=6.1.0
MONGODB_VERSION=1.17.2
YAML_VERSION=2.2.2
IMAGICK_VERSION=3.7.0

mkdir -p pool/ext
mkdir -p pool/lib
mkdir -p pool/php-tar

WORK_TEMP_DIR=${__PROJECT__}/var/cygwin-build/
EXT_TEMP_CACHE_DIR=${WORK_TEMP_DIR}/pool/ext/
mkdir -p ${WORK_TEMP_DIR}
mkdir -p ${EXT_TEMP_CACHE_DIR}
test -d ${WORK_TEMP_DIR}/ext/ && rm -rf ${WORK_TEMP_DIR}/ext/
mkdir -p ${WORK_TEMP_DIR}/ext/

download_and_extract() {
  local EXT_NAME=$1
  local EXT_VERSION=$2
  local EXT_URL="https://pecl.php.net/get/${EXT_NAME}-${EXT_VERSION}.tgz"

  cd ${__PROJECT__}/pool/ext
  if [ ! -f ${EXT_NAME}-${EXT_VERSION}.tgz ]; then
    curl -fSLo ${EXT_TEMP_CACHE_DIR}/${EXT_NAME}-${EXT_VERSION}.tgz ${EXT_URL}
    mv ${EXT_TEMP_CACHE_DIR}/${EXT_NAME}-${EXT_VERSION}.tgz ${__PROJECT__}/pool/ext
  fi

  mkdir -p ${WORK_TEMP_DIR}/ext/${EXT_NAME}/
  tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/${EXT_NAME}/ -xf ${EXT_NAME}-${EXT_VERSION}.tgz
}

# Download and extract extensions
download_and_extract "redis" ${REDIS_VERSION}

# mongodb 扩展 不支持 cygwin 环境下构建
# 详见： https://github.com/mongodb/mongo-php-driver/issues/1381
# download_and_extract "mongodb" ${MONGODB_VERSION}

download_and_extract "yaml" ${YAML_VERSION}
download_and_extract "imagick" ${IMAGICK_VERSION}

cd ${__PROJECT__}/pool/ext
if [ ! -f swoole-${SWOOLE_VERSION}.tgz ]; then
  test -d ${WORK_TEMP_DIR}/swoole && rm -rf ${WORK_TEMP_DIR}/swoole
  git clone -b ${SWOOLE_VERSION} https://github.com/swoole/swoole-src.git ${WORK_TEMP_DIR}/swoole
  cd ${WORK_TEMP_DIR}/swoole
  tar -czvf ${EXT_TEMP_CACHE_DIR}/swoole-${SWOOLE_VERSION}.tgz .
  mv ${EXT_TEMP_CACHE_DIR}/swoole-${SWOOLE_VERSION}.tgz ${__PROJECT__}/pool/ext
  cd ${__PROJECT__}/pool/ext
fi
mkdir -p ${WORK_TEMP_DIR}/ext/swoole/
tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/swoole/ -xf swoole-${SWOOLE_VERSION}.tgz

cd ${__PROJECT__}
# clean extension folder
NO_BUILT_IN_EXTENSIONS=$(ls ${WORK_TEMP_DIR}/ext/)
for EXT_NAME in $NO_BUILT_IN_EXTENSIONS; do
  echo "EXTENSION_NAME: $EXT_NAME "
  test -d ${__PROJECT__}/ext/${EXT_NAME} && rm -rf ${__PROJECT__}/ext/${EXT_NAME}
done

# download php-src source code
cd ${__PROJECT__}/pool/php-tar
if [ ! -f php-${PHP_VERSION}.tar.gz ]; then
  curl -fSLo php-${PHP_VERSION}.tar.gz https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.tar.gz
fi
test -d ${WORK_TEMP_DIR}/php-src && rm -rf ${WORK_TEMP_DIR}/php-src
mkdir -p ${WORK_TEMP_DIR}/php-src
tar --strip-components=1 -C ${WORK_TEMP_DIR}/php-src -xf php-${PHP_VERSION}.tar.gz

cd ${__PROJECT__}
# copy extension
# cp -rf var/cygwin-build/ext/* var/cygwin-build/php-src/ext/
cp -rf ${WORK_TEMP_DIR}/ext/* ${WORK_TEMP_DIR}/php-src/ext/

# extension hook
if [ "$X_PHP_VERSION" = "8.4" ]; then
  sed -i.backup "s/php_strtolower(/zend_str_tolower(/" ${WORK_TEMP_DIR}/php-src/ext/imagick/imagick.c
fi

# php source code hook
cd ${WORK_TEMP_DIR}/php-src
if [ "$X_PHP_VERSION" = "8.4" ] || [ "$X_PHP_VERSION" = "8.3" ] || [ "$X_PHP_VERSION" = "8.2" ] || [ "$X_PHP_VERSION" = "8.1" ]; then
  sed -i.backup 's/!defined(__HAIKU__)/!defined(__HAIKU__) \&\& !defined(__CYGWIN__)/' TSRM/TSRM.c
fi

cd ${__PROJECT__}
