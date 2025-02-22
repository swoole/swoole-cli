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

PHP_VERSION=$(cat ${__PROJECT__}/sapi/PHP-VERSION.conf)
SWOOLE_VERSION=v4.8.13

while [ $# -gt 0 ]; do
  case "$1" in
  --swoole-version)
    SWOOLE_VERSION="$2"
    ;;
  --*)
    echo "Illegal option $1"
    ;;
  esac
  shift $(($# > 0 ? 1 : 0))
done

PHP_VERSION=$(cat ${__PROJECT__}/sapi/PHP-VERSION.conf)

REDIS_VERSION=5.3.7
MONGODB_VERSION=1.14.2
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

cd ${__PROJECT__}/pool/ext
if [ ! -f redis-${REDIS_VERSION}.tgz ]; then
  curl -fSLo ${EXT_TEMP_CACHE_DIR}/redis-${REDIS_VERSION}.tgz https://pecl.php.net/get/redis-${REDIS_VERSION}.tgz
  mv ${EXT_TEMP_CACHE_DIR}/redis-${REDIS_VERSION}.tgz ${__PROJECT__}/pool/ext
fi
mkdir -p ${WORK_TEMP_DIR}/ext/redis/
tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/redis/ -xf redis-${REDIS_VERSION}.tgz

: <<'EOF'
# mongodb 扩展 不支持 cygwin 环境下构建
# 详见： https://github.com/mongodb/mongo-php-driver/issues/1381

cd ${__PROJECT__}/pool/ext
if [ ! -f mongodb-${MONGODB_VERSION}.tgz ]; then
  curl -fSLo ${EXT_TEMP_CACHE_DIR}/mongodb-${MONGODB_VERSION}.tgz https://pecl.php.net/get/mongodb-${MONGODB_VERSION}.tgz
  mv ${EXT_TEMP_CACHE_DIR}/redis-${REDIS_VERSION}.tgz ${__PROJECT__}/pool/ext
fi
mkdir -p ${WORK_TEMP_DIR}/ext/mongodb/
tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/mongodb/ -xf redis-${REDIS_VERSION}.tgz

EOF

cd ${__PROJECT__}/pool/ext
if [ ! -f yaml-${YAML_VERSION}.tgz ]; then
  curl -fSLo ${EXT_TEMP_CACHE_DIR}/yaml-${YAML_VERSION}.tgz https://pecl.php.net/get/yaml-${YAML_VERSION}.tgz
  mv ${EXT_TEMP_CACHE_DIR}/yaml-${YAML_VERSION}.tgz ${__PROJECT__}/pool/ext
fi
mkdir -p ${WORK_TEMP_DIR}/ext/yaml/
tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/yaml/ -xf yaml-${YAML_VERSION}.tgz

cd ${__PROJECT__}/pool/ext
if [ ! -f imagick-${IMAGICK_VERSION}.tgz ]; then
  curl -fSLo ${EXT_TEMP_CACHE_DIR}/imagick-${IMAGICK_VERSION}.tgz https://pecl.php.net/get/imagick-${IMAGICK_VERSION}.tgz
  mv ${EXT_TEMP_CACHE_DIR}/imagick-${IMAGICK_VERSION}.tgz ${__PROJECT__}/pool/ext
fi
mkdir -p ${WORK_TEMP_DIR}/ext/imagick/
tar --strip-components=1 -C ${WORK_TEMP_DIR}/ext/imagick/ -xf imagick-${IMAGICK_VERSION}.tgz

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
# cp -rf var/cygwin-build/ext/* ext/
cp -rf ${WORK_TEMP_DIR}/ext/* ${__PROJECT__}/ext/

# extension hook

cd ${__PROJECT__}
