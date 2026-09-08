#!/usr/bin/env bash

set -euo pipefail

WORK_DIR=${WORK_DIR:?WORK_DIR is required}
GLOBAL_PREFIX=${GLOBAL_PREFIX:?GLOBAL_PREFIX is required}
PHPX_DIR=${PHPX_DIR:-${WORK_DIR}/thirdparty/phpx}
SDK_ROOT=${PHPX_IOS_SDK_DIR:-${PHPX_DIR}/ios/iphoneos-arm64}

if [[ $(basename "${SDK_ROOT}") != iphoneos-arm64
    || $(basename "$(dirname "${SDK_ROOT}")") != ios ]]; then
    echo "Refusing to replace an unexpected SDK path: ${SDK_ROOT}" >&2
    exit 1
fi

if [[ $(uname -s) != Darwin ]]; then
    echo "The iPhoneOS SDK must be staged on macOS." >&2
    exit 1
fi
xcrun --sdk iphoneos --show-sdk-path >/dev/null

required_files=(
    "${WORK_DIR}/libs/libphp.a"
    "${WORK_DIR}/main/php.h"
    "${WORK_DIR}/main/php_config.h"
    "${GLOBAL_PREFIX}/gmp/include/gmp.h"
    "${GLOBAL_PREFIX}/gmp/include/gmpxx.h"
    "${GLOBAL_PREFIX}/gmp/lib/libgmp.a"
    "${GLOBAL_PREFIX}/gmp/lib/libgmpxx.a"
    "${GLOBAL_PREFIX}/mpfr/include/mpfr.h"
    "${GLOBAL_PREFIX}/mpfr/lib/libmpfr.a"
)
for file in "${required_files[@]}"; do
    if [[ ! -f "${file}" ]]; then
        echo "The iPhoneOS SDK input is missing: ${file}" >&2
        exit 1
    fi
done

if ! grep -Eq '^#define[[:space:]]+ZTS([[:space:]]+1)?([[:space:]]|$)' "${WORK_DIR}/main/php_config.h"; then
    echo "The iPhoneOS PHP build is not ZTS: ${WORK_DIR}/main/php_config.h" >&2
    exit 1
fi

rm -rf "${SDK_ROOT}"
mkdir -p "${SDK_ROOT}/include/php" "${SDK_ROOT}/include" "${SDK_ROOT}/lib"

(
    cd "${WORK_DIR}"
    find main Zend TSRM ext sapi/embed -name '*.h' -print \
        | tar -cf - -T - \
        | tar -xf - -C "${SDK_ROOT}/include/php"
)

cp -p "${GLOBAL_PREFIX}/gmp/include/gmp.h" "${SDK_ROOT}/include/"
cp -p "${GLOBAL_PREFIX}/gmp/include/gmpxx.h" "${SDK_ROOT}/include/"
cp -p "${GLOBAL_PREFIX}/mpfr/include/mpfr.h" "${SDK_ROOT}/include/"
if [[ -f "${GLOBAL_PREFIX}/mpfr/include/mpf2mpfr.h" ]]; then
    cp -p "${GLOBAL_PREFIX}/mpfr/include/mpf2mpfr.h" "${SDK_ROOT}/include/"
fi

cp -p "${WORK_DIR}/libs/libphp.a" "${SDK_ROOT}/lib/"
cp -p "${GLOBAL_PREFIX}/gmp/lib/libgmp.a" "${SDK_ROOT}/lib/"
cp -p "${GLOBAL_PREFIX}/gmp/lib/libgmpxx.a" "${SDK_ROOT}/lib/"
cp -p "${GLOBAL_PREFIX}/mpfr/lib/libmpfr.a" "${SDK_ROOT}/lib/"

for archive in libphp.a libgmp.a libgmpxx.a libmpfr.a; do
    archs=$(xcrun lipo -archs "${SDK_ROOT}/lib/${archive}")
    if [[ " ${archs} " != *" arm64 "* ]]; then
        echo "${archive} does not contain the required arm64 architecture: ${archs}" >&2
        exit 1
    fi
done

printf '%s\n' 'typephp-iphoneos-arm64-php-zts-abi-v1' > "${SDK_ROOT}/.typephp-ios-php-abi"
echo "Staged iPhoneOS PHP SDK inputs: ${SDK_ROOT}"
