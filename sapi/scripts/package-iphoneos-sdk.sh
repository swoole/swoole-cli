#!/usr/bin/env bash

set -euo pipefail

WORK_DIR=${WORK_DIR:?WORK_DIR is required}
PHPX_DIR=${PHPX_DIR:-${WORK_DIR}/thirdparty/phpx}
SDK_ROOT=${PHPX_IOS_SDK_DIR:-${PHPX_DIR}/ios/iphoneos-arm64}

SWOOLE_VERSION=$(awk 'NR==1{ print $1 }' "${WORK_DIR}/sapi/SWOOLE-VERSION.conf")
PHP_VERSION=$(awk 'NR==1{ print $1 }' "${WORK_DIR}/sapi/PHP-VERSION.conf")
SDK_NAME="swoole-cli-sdk-v${SWOOLE_VERSION#v}-php${PHP_VERSION}-iphoneos-arm64"
DIST_DIR="${WORK_DIR}/sdk"
PACKAGE_ROOT="${DIST_DIR}/${SDK_NAME}"

required_files=(
    .typephp-ios-sdk-abi
    include/php/main/php.h
    include/phpx/phpx.h
    include/gmp.h
    include/mpfr.h
    lib/libphp.a
    lib/libphpx.a
    lib/libgmp.a
    lib/libgmpxx.a
    lib/libmpfr.a
)
for file in "${required_files[@]}"; do
    if [[ ! -f "${SDK_ROOT}/${file}" ]]; then
        echo "The integrated iPhoneOS SDK is incomplete: ${SDK_ROOT}/${file}" >&2
        exit 1
    fi
done

rm -rf "${PACKAGE_ROOT}"
mkdir -p "${PACKAGE_ROOT}"
cp -R "${SDK_ROOT}/." "${PACKAGE_ROOT}/"

mkdir -p "${DIST_DIR}"
tar -C "${DIST_DIR}" -cJf "${DIST_DIR}/${SDK_NAME}.tar.xz" "${SDK_NAME}"

echo "Packaged iPhoneOS SDK: ${DIST_DIR}/${SDK_NAME}.tar.xz"
