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

PHP_VERSION=""
SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "${__PROJECT__}/sapi/SWOOLE-VERSION.conf")
X_PHP_VERSION=${PHP_VERSION}

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

REDIS_VERSION=6.2.0
YAML_VERSION=2.2.2
IMAGICK_VERSION=3.8.0
PHP_VERSION=$(awk 'NR==1' ${__PROJECT__}/sapi/PHP-VERSION.conf)

mkdir -p pool/ext
mkdir -p pool/lib
mkdir -p pool/php-tar

WORK_TEMP_DIR=${__PROJECT__}/var/msys2-build/
EXT_TEMP_CACHE_DIR=${WORK_TEMP_DIR}/pool/ext/
mkdir -p ${WORK_TEMP_DIR}
mkdir -p ${EXT_TEMP_CACHE_DIR}
test -d ${WORK_TEMP_DIR}/ext/ && rm -rf ${WORK_TEMP_DIR}/ext/
mkdir -p ${WORK_TEMP_DIR}/ext/

cd ${__PROJECT__}/pool/ext

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
set +u
if [ -n "${GITHUB_ACTION}" ]; then
  test -f ${__PROJECT__}/pool/ext/swoole-${SWOOLE_VERSION}.tgz && rm -f ${__PROJECT__}/pool/ext/swoole-${SWOOLE_VERSION}.tgz
fi
set -u
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
# cp -rf var/msys2-build/ext/* ext/
cp -rf ${WORK_TEMP_DIR}/ext/. ${__PROJECT__}/ext/
mkdir -p ${__PROJECT__}/ext/pgsql/
cp -rf ${WORK_TEMP_DIR}/php-src/ext/pgsql/. ${__PROJECT__}/ext/pgsql/

# extension hook

# php source code hook

cd ${__PROJECT__}
